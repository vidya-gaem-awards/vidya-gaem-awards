<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\Response;
use VGA\Model\Category;
use VGA\Model\News;
use VGA\Model\UserNomination;

class NomineeController extends BaseController
{
    public function indexAction()
    {
        $query = $this->em->createQueryBuilder();
        $query
            ->addSelect('c.id')
            ->addSelect('c.name')
            ->addSelect('un.nomination')
            ->addSelect('COUNT(un.id) as total')
            ->from(Category::class, 'c')
            ->join('c.userNominations', 'un')
            ->where('c.enabled = true')
            ->groupBy('un.nomination')
            ->addGroupBy('c.id')
            ->orderBy('c.order', 'ASC')
            ->addOrderBy('total', 'DESC')
            ->addOrderBy('un.nomination', 'ASC');

        if (!$this->user->canDo('categories-secret')) {
            $query->andWhere('c.secret = false');
        }

        $nominees = $query->getQuery()->getResult();

        $tpl = $this->twig->loadTemplate('nominees.twig');

        $response = new Response($tpl->render([
            'title' => 'Nominees',
            'nominees' => $nominees
        ]));
        $response->send();
    }
}
