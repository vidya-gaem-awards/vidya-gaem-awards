<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Award;
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

    public function promoAction(EntityManagerInterface $em)
    {
        $awards = $em->createQueryBuilder()
            ->select('a')
            ->from(Award::class, 'a')
            ->where('a.secret = false')
            ->andWhere('a.enabled = true')
            ->orderBy('a.order', 'ASC')
            ->getQuery()
            ->getResult();

        shuffle($awards);

        return $this->render('promo.html.twig', [
            'awards' => $awards
        ]);
    }
}
