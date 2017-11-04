<?php
namespace AppBundle\Controller;

use AppBundle\Service\ConfigService;
use AppBundle\Service\NavbarService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function indexAction(NavbarService $navbar, EntityManagerInterface $em, UserInterface $user)
    {
        if (!$navbar->canAccessRoute('awards')) {
            throw $this->createAccessDeniedException();
        }
        
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

        return $this->render('awards.twig', [
            'title' => 'Awards and Nominations',
            'awards' => $awards,
            'userNominations' => $nominations,
            'userOpinions' => $opinions,
            'autocompleters' => $autocompleters
        ]);
    }

    public function postAction(NavbarService $navbar, Request $request, EntityManagerInterface $em, ConfigService $configService, UserInterface $user)
    {
        if (!$navbar->canAccessRoute('awards')) {
            throw $this->createAccessDeniedException();
        }
        
        $post = $request->request;
        $repo = $em->getRepository(Award::class);
        $response = new JsonResponse();

        /** @var Award $award */
        $award = $repo->find($post->get('id'));

        if (!$award || $award->isSecret() || !$award->isEnabled()) {
            $response->setData(['error' => 'Invalid award provided.']);
            $response->send();
            return;
        }

        $opinion = $post->get('opinion');
        if ($opinion !== null) {
            if ($configService->isReadOnly()) {
                $response->setData(['error' => 'Feedback can no longer be given on awards.']);
                $response->send();
                return;
            }

            if (!in_array($opinion, ['-1', '1', '0'], true)) {
                $response->setData(['error' => 'Invalid opinion provided.']);
                $response->send();
                return;
            }

            $opinionRepo = $em->getRepository(AwardFeedback::class);
            /** @var AwardFeedback $feedback */
            $feedback = $opinionRepo->findOneBy(['award' => $award, 'user' => $user->getFuzzyID()]);
            if (!$feedback) {
                $feedback = new AwardFeedback($award, $user);
            }
            $feedback->setOpinion($opinion);
            $em->persist($feedback);

            $action = new Action('opinion-given');
            $action->setUser($user)
                ->setPage(__CLASS__)
                ->setData1($award->getId())
                ->setData2($opinion);

            $em->persist($action);
        }

        $nomination = $post->get('nomination');
        if ($nomination !== null) {
            if ($configService->isReadOnly()) {
                $response->setData(['error' => 'Nominations can no longer be made for this award.']);
                $response->send();
                return;
            }

            if (!$award->areNominationsEnabled()) {
                $response->setData(['error' => 'Nominations aren\'t currently open for this award.']);
                $response->send();
                return;
            }

            $nomination = trim($nomination);
            if ($nomination === '') {
                $response->setData(['error' => 'Nomination cannot be blank.']);
                $response->send();
                return;
            }

            $result = $em->createQueryBuilder()
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
                $response->setData(['error' => 'You\'ve already nominated that.']);
                $response->send();
                return;
            }

            $userNomination = new UserNomination($award, $user, $nomination);
            $em->persist($userNomination);

            $action = new Action('nomination-made');
            $action->setUser($user)
                ->setPage(__CLASS__)
                ->setData1($award->getId())
                ->setData2($nomination);
            $em->persist($action);
        }

        $em->flush();

        $response->setData(['success' => true]);
        $response->send();
    }
}
