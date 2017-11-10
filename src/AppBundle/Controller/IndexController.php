<?php
namespace AppBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\News;

class IndexController extends Controller
{
    public function indexAction(EntityManagerInterface $em)
    {
        $query = $em->createQueryBuilder();
        $query->select('n')
            ->from(News::class, 'n')
            ->where('n.visible = true')
            ->andWhere('n.timestamp < CURRENT_TIMESTAMP()')
            ->setMaxResults(5)
            ->orderBy('n.timestamp', 'DESC');

        $news = $query->getQuery()->getResult();

        return $this->render('index.html.twig', [
            'title' => 'Home',
            'news' => $news
        ]);
    }
}
