<?php
namespace App\Controller;

use App\Entity\Award;
use App\Entity\News;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class IndexController extends AbstractController
{
    public function indexAction(EntityManagerInterface $em): Response
    {
        $query = $em->createQueryBuilder();
        $query->select('n')
            ->from(News::class, 'n')
            ->where('n.visible = true')
            ->andWhere('n.timestamp < CURRENT_TIMESTAMP()')
//            ->setMaxResults(5)
            ->orderBy('n.timestamp', 'DESC');

        $news = $query->getQuery()->getResult();

        return $this->render('index.html.twig', [
            'title' => 'Home',
            'news' => $news
        ]);
    }

    public function promoAction(EntityManagerInterface $em, SessionInterface $session): Response
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

        // The full animation takes 10 seconds to run. This gets really tedious, so show a much shorter animation
        // on every subsequent page load.
        $fastAnimations = $session->get('fastPromoAnimations', true); // currently defaulted to true
        if (!$fastAnimations) {
            $session->set('fastPromoAnimations', true);
        }

        return $this->render('promo.html.twig', [
            'awards' => $awards,
            'fastAnimations' => $fastAnimations,
        ]);
    }
}
