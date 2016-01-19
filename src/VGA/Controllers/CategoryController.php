<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\Response;
use VGA\Model\Category;
use VGA\Model\CategoryFeedback;
use VGA\Model\News;
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

        $repo = $this->em->getRepository(UserNomination::class);
        $query = $repo->createQueryBuilder('un');
        $query->select('un')
            ->where('un.user = :user')
            ->setParameter('user', $this->user->getFuzzyID());
        $result = $query->getQuery()->getResult();

        $nominations = array_fill_keys(array_keys($categories), []);

        /** @var UserNomination $un */
        foreach ($result as $un) {
            $nominations[$un->getCategory()->getId()][] = $un->getNomination();
        }

        $repo = $this->em->getRepository(CategoryFeedback::class);
        $query = $repo->createQueryBuilder('cf')
            ->where('cf.user = :user')
            ->setParameter('user', $this->user->getFuzzyID());
        $result = $query->getQuery()->getResult();

        $opinions = array_fill_keys(array_keys($categories), 0);

        /** @var CategoryFeedback $cf */
        foreach ($result as $cf) {
            $opinions[$cf->getCategory()->getId()] = $cf->getOpinion();
        }

        $tpl = $this->twig->loadTemplate('categories.twig');

        $response = new Response($tpl->render([
            'title' => 'Awards and Nominations',
            'categories' => $categories,
            'userNominations' => $nominations,
            'userOpinions' => $opinions
        ]));
        $response->send();
    }
}
