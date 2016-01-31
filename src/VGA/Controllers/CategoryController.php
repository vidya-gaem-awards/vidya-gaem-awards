<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use VGA\Model\Action;
use VGA\Model\Autocompleter;
use VGA\Model\Category;
use VGA\Model\CategoryFeedback;
use VGA\Model\GameRelease;
use VGA\Model\UserNomination;

class CategoryController extends BaseController
{
    public function indexAction()
    {
        $repo = $this->em->getRepository(Category::class);
        $query = $repo->createQueryBuilder('c', 'c.id');
        $query->select('c')
            ->where('c.enabled = true')
            ->andWhere('c.secret = false')
            ->orderBy('c.order', 'ASC');
        $categories = $query->getQuery()->getResult();

        $nominationRepo = $this->em->getRepository(UserNomination::class);
        $query = $nominationRepo->createQueryBuilder('un');
        $query->select('un')
            ->where('un.user = :user')
            ->setParameter('user', $this->user->getFuzzyID());
        $result = $query->getQuery()->getResult();

        $nominations = array_fill_keys(array_keys($categories), []);

        /** @var UserNomination $un */
        foreach ($result as $un) {
            $nominations[$un->getCategory()->getId()][] = $un->getNomination();
        }

        $feedbackRepo = $this->em->getRepository(CategoryFeedback::class);
        $query = $feedbackRepo->createQueryBuilder('cf')
            ->where('cf.user = :user')
            ->setParameter('user', $this->user->getFuzzyID());
        $result = $query->getQuery()->getResult();

        $opinions = array_fill_keys(array_keys($categories), 0);

        /** @var CategoryFeedback $cf */
        foreach ($result as $cf) {
            $opinions[$cf->getCategory()->getId()] = $cf->getOpinion();
        }

        $autocompleterRepo = $this->em->getRepository(Autocompleter::class);
        $result = $autocompleterRepo->findAll();

        $autocompleters = array_fill_keys(array_keys($categories), []);

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

        /** @var Category $category */
        foreach ($categories as $category) {
            // Don't bother populating the autocompleter for this category if it already has a different one defined
            if ($category->getAutocompleter()) {
                continue;
            }

            $allNominations = array_map(function ($un) {
                /** @var UserNomination $un */
                return $un->getNomination();
            }, $category->getRawUserNominations()->toArray());

            $nominationCount = array_fill_keys(array_values($allNominations), 0);
            foreach ($allNominations as $nomination) {
                $nominationCount[$nomination]++;
            }

            $nominationCount = array_filter($nominationCount, function ($count) {
                return $count >= 2;
            }, ARRAY_FILTER_USE_BOTH);

            $autocompleters[$category->getId()] = array_keys($nominationCount);
        }

        $tpl = $this->twig->loadTemplate('categories.twig');

        $response = new Response($tpl->render([
            'title' => 'Awards and Nominations',
            'categories' => $categories,
            'userNominations' => $nominations,
            'userOpinions' => $opinions,
            'autocompleters' => $autocompleters
        ]));
        $response->send();
    }

    public function postAction()
    {
        $post = $this->request->request;
        $repo = $this->em->getRepository(Category::class);
        $response = new JsonResponse();

        /** @var Category $category */
        $category = $repo->find($post->get('id'));

        if (!$category || $category->isSecret() || !$category->isEnabled()) {
            $response->setData(['error' => 'Invalid category provided.']);
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

            $opinionRepo = $this->em->getRepository(CategoryFeedback::class);
            /** @var CategoryFeedback $feedback */
            $feedback = $opinionRepo->findOneBy(['category' => $category, 'user' => $this->user->getFuzzyID()]);
            if (!$feedback) {
                $feedback = new CategoryFeedback($category, $this->user);
            }
            $feedback->setOpinion($opinion);
            $this->em->persist($feedback);

            $action = new Action('opinion-given');
            $action->setUser($this->user)
                ->setPage(__CLASS__)
                ->setData1($category->getId())
                ->setData2($opinion);

            $this->em->persist($action);
        }

        $nomination = $post->get('nomination');
        if ($nomination !== null) {

            if (!$category->areNominationsEnabled()) {
                $response->setData(['error' => 'Nominations have been closed for this award.']);
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
                ->andWhere('IDENTITY(n.category) = :category')
                ->andWhere('LOWER(n.nomination) = :nomination')
                ->setParameter('fuzzyUser', $this->user->getFuzzyID())
                ->setParameter('category', $category->getId())
                ->setParameter('nomination', strtolower($nomination))
                ->getQuery()
                ->getOneOrNullResult();

            if ($result) {
                $response->setData(['error' => 'You\'ve already nominated that.']);
                $response->send();
                return;
            }

            $userNomination = new UserNomination($category, $this->user, $nomination);
            $this->em->persist($userNomination);

            $action = new Action('nomination-made');
            $action->setUser($this->user)
                ->setPage(__CLASS__)
                ->setData1($category->getId())
                ->setData2($nomination);
            $this->em->persist($action);
        }

        $this->em->flush();

        $response->setData(['success' => true]);
        $response->send();
    }
}
