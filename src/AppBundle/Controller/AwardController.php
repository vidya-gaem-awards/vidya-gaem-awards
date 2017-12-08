<?php
namespace AppBundle\Controller;

use AppBundle\Entity\AwardSuggestion;
use AppBundle\Entity\User;
use AppBundle\Service\AuditService;
use AppBundle\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Action;
use AppBundle\Entity\Autocompleter;
use AppBundle\Entity\Award;
use AppBundle\Entity\AwardFeedback;
use AppBundle\Entity\GameRelease;
use AppBundle\Entity\UserNomination;
use Symfony\Component\Security\Core\User\UserInterface;

class AwardController extends Controller
{
    public function indexAction(EntityManagerInterface $em, UserInterface $user)
    {
        /** @var User $user */

        $awards = $em->createQueryBuilder()
            ->select('a')
            ->from(Award::class, 'a', 'a.id')
            ->where('a.enabled = true')
            ->andWhere('a.secret = false')
            ->orderBy('a.order', 'ASC')
            ->getQuery()
            ->getResult();

        $userNominations = $em->createQueryBuilder()
            ->select('un')
            ->from(UserNomination::class, 'un')
            ->where('un.user = :user')
            ->setParameter('user', $user->getFuzzyID())
            ->getQuery()
            ->getResult();
        
        $nominations = array_fill_keys(array_keys($awards), []);

        /** @var UserNomination $un */
        foreach ($userNominations as $un) {
            $nominations[$un->getAward()->getId()][] = $un->getNomination();
        }

        $feedback = $em->createQueryBuilder()
            ->select('af')
            ->from(AwardFeedback::class,'af')
            ->where('af.user = :user')
            ->setParameter('user', $user->getFuzzyID())
            ->getQuery()
            ->getResult();

        $opinions = array_fill_keys(array_keys($awards), 0);

        /** @var AwardFeedback $cf */
        foreach ($feedback as $cf) {
            $opinions[$cf->getAward()->getId()] = $cf->getOpinion();
        }

        $userSuggestions = $em->createQueryBuilder()
            ->select('s')
            ->from(AwardSuggestion::class, 's')
            ->where('s.user = :user')
            ->setParameter('user', $user->getFuzzyID())
            ->getQuery()
            ->getResult();

        $suggestions = array_fill_keys(array_keys($awards), []);

        /** @var AwardSuggestion $suggestion */
        foreach ($userSuggestions as $suggestion) {
            if ($suggestion->getAward()) {
                $key = $suggestion->getAward()->getId();
            } else {
                $key = 'new-award';
            }
            $suggestions[$key][] = $suggestion->getSuggestion();
        }

        $autocompleterRepo = $em->getRepository(Autocompleter::class);
        $result = $autocompleterRepo->findAll();

        $autocompleters = array_fill_keys(array_keys($awards), []);

        /** @var Autocompleter $autocompleter */
        foreach ($result as $autocompleter) {
            $strings = $autocompleter->getStrings();
            // The video-game autocompleter is a special case: its values are stored in another table
            if ($autocompleter->getId() === 'video-game') {
                $gameRepo = $em->getRepository(GameRelease::class);
                $games = $gameRepo->findAll();
                /** @var GameRelease $game */
                foreach ($games as $game) {
                    $strings[] = [
                        'value' => $game->getName(),
                        'label' => $game->getName() . ' (' . implode(', ', $game->getPlatforms()) . ')'
                    ];
                }
            }
            $autocompleters[$autocompleter->getId()] = $strings;
        }

        /** @var Award $award */
        foreach ($awards as $award) {
            // Don't bother populating the autocompleter for this award if it already has a different one defined
            if ($award->getAutocompleter()) {
                continue;
            }

            $allNominations = array_map(function ($un) {
                /** @var UserNomination $un */
                return $un->getNomination();
            }, $award->getRawUserNominations()->toArray());

            $nominationCount = array_fill_keys(array_values($allNominations), 0);
            foreach ($allNominations as $nomination) {
                $nominationCount[$nomination]++;
            }

            $nominationCount = array_filter($nominationCount, function ($count) {
                return $count >= 2;
            }, ARRAY_FILTER_USE_BOTH);

            $autocompleters[$award->getId()] = array_keys($nominationCount);
        }

        return $this->render('awards.html.twig', [
            'title' => 'Awards and Nominations',
            'awards' => $awards,
            'userNominations' => $nominations,
            'userOpinions' => $opinions,
            'userSuggestions' => $suggestions,
            'autocompleters' => $autocompleters
        ]);
    }

    public function postAction(Request $request, EntityManagerInterface $em, ConfigService $configService, UserInterface $user, AuditService $auditService)
    {
        /** @var User $user */
        
        $post = $request->request;
        $repo = $em->getRepository(Award::class);

        $awardSuggestion = $post->get('awardSuggestion');
        if ($awardSuggestion !== null) {
            if ($configService->isReadOnly()) {
                return $this->json(['error' => 'New awards can no longer be suggested.']);
            }

            $awardSuggestion = trim($awardSuggestion);
            if ($awardSuggestion === '') {
                return $this->json(['error' => 'Your award idea cannot be blank.']);
            }

            $result = $em->createQueryBuilder()
                ->select('s')
                ->from(AwardSuggestion::class, 's')
                ->where('s.user = :fuzzyUser')
                ->andWhere('s.award IS NULL')
                ->andWhere('LOWER(s.suggestion) = :suggestion')
                ->setParameter('fuzzyUser', $user->getFuzzyID())
                ->setParameter('suggestion', strtolower($awardSuggestion))
                ->getQuery()
                ->getOneOrNullResult();

            if ($result) {
                return $this->json(['error' => 'You\'ve already suggested that award.']);
            }

            $suggestion = new AwardSuggestion();
            $suggestion
                ->setSuggestion($awardSuggestion)
                ->setUser($user);

            $em->persist($suggestion);

            $auditService->add(
                new Action('new-award-suggested', $awardSuggestion)
            );

            $em->flush();
            return $this->json(['success' => true]);
        }

        /** @var Award $award */
        $award = $repo->find($post->get('id'));

        if (!$award || $award->isSecret() || !$award->isEnabled()) {
            return $this->json(['error' => 'Invalid award provided.']);
        }

        $opinion = $post->get('opinion');
        if ($opinion !== null) {
            if ($configService->isReadOnly()) {
                return $this->json(['error' => 'Feedback can no longer be given on awards.']);
            }

            if (!in_array($opinion, ['-1', '1', '0'], true)) {
                return $this->json(['error' => 'Invalid opinion provided.']);
            }

            $opinionRepo = $em->getRepository(AwardFeedback::class);
            /** @var AwardFeedback $feedback */
            $feedback = $opinionRepo->findOneBy(['award' => $award, 'user' => $user->getFuzzyID()]);
            if (!$feedback) {
                $feedback = new AwardFeedback($award, $user);
            }
            $feedback->setOpinion($opinion);
            $em->persist($feedback);

            $auditService->add(
                new Action('opinion-given', $award->getId(), $opinion)
            );

            $em->flush();
            return $this->json(['success' => true]);
        }

        $suggestedName = $post->get('suggestedName');
        if ($suggestedName !== null) {
            if ($configService->isReadOnly()) {
                return $this->json(['error' => 'Name suggestions can no longer be made for this award.']);
            }

            $suggestedName = trim($suggestedName);
            if ($suggestedName === '') {
                return $this->json(['error' => 'Suggested award name cannot be blank.']);
            }

            $result = $em->createQueryBuilder()
                ->select('s')
                ->from(AwardSuggestion::class, 's')
                ->where('s.user = :fuzzyUser')
                ->andWhere('IDENTITY(s.award) = :award')
                ->andWhere('LOWER(s.suggestion) = :suggestion')
                ->setParameter('fuzzyUser', $user->getFuzzyID())
                ->setParameter('award', $award->getId())
                ->setParameter('suggestion', strtolower($suggestedName))
                ->getQuery()
                ->getOneOrNullResult();

            if ($result) {
                return $this->json(['error' => 'You\'ve already suggested that name.']);
            }

            $suggestion = new AwardSuggestion();
            $suggestion
                ->setAward($award)
                ->setSuggestion($suggestedName)
                ->setUser($user);

            $em->persist($suggestion);

            $auditService->add(
                new Action('award-name-suggested', $award->getId(), $suggestedName)
            );

            $em->flush();
            return $this->json(['success' => true]);
        }

        $nomination = $post->get('nomination');
        if ($nomination !== null) {
            if ($configService->isReadOnly()) {
                return $this->json(['error' => 'Nominations can no longer be made for this award.']);
            }

            if (!$award->areNominationsEnabled()) {
                return $this->json(['error' => 'Nominations aren\'t currently open for this award.']);
            }

            $nomination = trim($nomination);
            if ($nomination === '') {
                return $this->json(['error' => 'Nomination cannot be blank.']);
            }

            $result = $em->createQueryBuilder()
                ->select('un')
                ->from(UserNomination::class, 'un')
                ->where('un.user = :fuzzyUser')
                ->andWhere('IDENTITY(un.award) = :award')
                ->andWhere('LOWER(un.nomination) = :nomination')
                ->setParameter('fuzzyUser', $user->getFuzzyID())
                ->setParameter('award', $award->getId())
                ->setParameter('nomination', strtolower($nomination))
                ->getQuery()
                ->getOneOrNullResult();

            if ($result) {
                return $this->json(['error' => 'You\'ve already nominated that.']);
            }

            $userNomination = new UserNomination($award, $user, $nomination);
            $em->persist($userNomination);

            $auditService->add(
                new Action('nomination-made', $award->getId(), $nomination)
            );

            $em->flush();
            return $this->json(['success' => true]);
        }

        return $this->json(['error' => 'An unexpected error occurred.']);
    }
}
