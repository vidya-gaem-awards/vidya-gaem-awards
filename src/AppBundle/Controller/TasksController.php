<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Nominee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TasksController extends Controller
{
    public function indexAction(EntityManagerInterface $em)
    {
        $noFlavourText = $em->createQueryBuilder()
            ->select('n')
            ->from(Nominee::class, 'n')
            ->join('n.award', 'a')
            ->where('a.enabled = true')
            ->andWhere('n.flavorText = \'\'')
            ->getQuery()
            ->getResult();

        $noImage = $em->createQueryBuilder()
            ->select('n')
            ->from(Nominee::class, 'n')
            ->join('n.award', 'a')
            ->where('a.enabled = 1')
            ->andWhere('n.image IS NULL')
            ->getQuery()
            ->getResult();

        $noSubtitle = $em->createQueryBuilder()
            ->select('n')
            ->from(Nominee::class, 'n')
            ->join('n.award', 'a')
            ->where('a.enabled = true')
            ->andWhere('n.subtitle = \'\'')
            ->getQuery()
            ->getResult();

        $totalNominees = $em->createQueryBuilder()
            ->select('COUNT(n)')
            ->from(Nominee::class, 'n')
            ->join('n.award', 'a')
            ->where('a.enabled = true')
            ->getQuery()
            ->getSingleScalarResult();

        $tasks = [
            'Nominees with a subtitle' => $totalNominees - count($noSubtitle),
            'Nominees with flavour text' => $totalNominees - count($noFlavourText),
            'Nominees with an image' => $totalNominees - count($noImage),
        ];

        return $this->render('tasks.html.twig', [
            'title' => 'Tasks',
            'tasks' => $tasks,
            'totalNominees' => $totalNominees
        ]);
    }
}
