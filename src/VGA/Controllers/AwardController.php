<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use VGA\Model\Action;
use VGA\Model\Autocompleter;
use VGA\Model\Award;
use VGA\Model\AwardFeedback;
use VGA\Model\GameRelease;
use VGA\Model\UserNomination;

class AwardController extends BaseController
{
    public function indexAction()
    {
        $repo = $this->em->getRepository(Award::class);
        $query = $repo->createQueryBuilder('c', 'c.id');
        $query->select('c')
            ->where('c.enabled = true')
            ->andWhere('c.secret = false')
            ->orderBy('c.order', 'ASC');
        $awards = $query->getQuery()->getResult();

        $nominationRepo = $this->em->getRepository(UserNomination::class);
        $query = $nominationRepo->createQueryBuilder('un');
        $query->select('un')
            ->where('un.user = :user')
            ->setParameter('user', $this->user->getFuzzyID());
        $result = $query->getQuery()->getResult();

        $nominations = array_fill_keys(array_keys($awards), []);

        /** @var UserNomination $un */
        foreach ($result as $un) {
            $nominations[$un->getAward()->getId()][] = $un->getNomination();
        }

        $feedbackRepo = $this->em->getRepository(AwardFeedback::class);
        $query = $feedbackRepo->createQueryBuilder('cf')
            ->where('cf.user = :user')
            ->setParameter('user', $this->user->getFuzzyID());
        $result = $query->getQuery()->getResult();

        $opinions = array_fill_keys(array_keys($awards), 0);

        /** @var AwardFeedback $cf */
        foreach ($result as $cf) {
            $opinions[$cf->getAward()->getId()] = $cf->getOpinion();
        }

        $autocompleterRepo = $this->em->getRepository(Autocompleter::class);
        $result = $autocompleterRepo->findAll();

        $autocompleters = array_fill_keys(array_keys($awards), []);

        /** @var Autocompleter $autocompleter */
        foreach ($result as $autocompleter) {
            $strings = $autocompleter->getStrings();
            // The video-game autocompleter is a special case: its values are stored in another table
            if ($autocompleter->getId() === 'video-game') {
                $gameRepo = $this->em->getRepository(GameRelease::class);
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

        $tpl = $this->twig->loadTemplate('awards.twig');

        $response = new Response($tpl->render([
            'title' => 'Awards and Nominations',
            'awards' => $awards,
            'userNominations' => $nominations,
            'userOpinions' => $opinions,
            'autocompleters' => $autocompleters
        ]));
        $response->send();
    }

    public function postAction()
    {
        $post = $this->request->request;
        $repo = $this->em->getRepository(Award::class);
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
            if (!in_array($opinion, ['-1', '1', '0'], true)) {
                $response->setData(['error' => 'Invalid opinion provided.']);
                $response->send();
                return;
            }

            $opinionRepo = $this->em->getRepository(AwardFeedback::class);
            /** @var AwardFeedback $feedback */
            $feedback = $opinionRepo->findOneBy(['award' => $award, 'user' => $this->user->getFuzzyID()]);
            if (!$feedback) {
                $feedback = new AwardFeedback($award, $this->user);
            }
            $feedback->setOpinion($opinion);
            $this->em->persist($feedback);

            $action = new Action('opinion-given');
            $action->setUser($this->user)
                ->setPage(__CLASS__)
                ->setData1($award->getId())
                ->setData2($opinion);

            $this->em->persist($action);
        }

        $nomination = $post->get('nomination');
        if ($nomination !== null) {

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

            $nominationRepo = $this->em->getRepository(UserNomination::class);
            $result = $nominationRepo->createQueryBuilder('n')
                ->where('n.user = :fuzzyUser')
                ->andWhere('IDENTITY(n.award) = :award')
                ->andWhere('LOWER(n.nomination) = :nomination')
                ->setParameter('fuzzyUser', $this->user->getFuzzyID())
                ->setParameter('award', $award->getId())
                ->setParameter('nomination', strtolower($nomination))
                ->getQuery()
                ->getOneOrNullResult();

            if ($result) {
                $response->setData(['error' => 'You\'ve already nominated that.']);
                $response->send();
                return;
            }

            $userNomination = new UserNomination($award, $this->user, $nomination);
            $this->em->persist($userNomination);

            $action = new Action('nomination-made');
            $action->setUser($this->user)
                ->setPage(__CLASS__)
                ->setData1($award->getId())
                ->setData2($nomination);
            $this->em->persist($action);
        }

        $this->em->flush();

        $response->setData(['success' => true]);
        $response->send();
    }
}
