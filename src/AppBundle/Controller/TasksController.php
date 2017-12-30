<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Nominee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TasksController extends Controller
{
    public function indexAction(EntityManagerInterface $em)
    {
        $flavourTextCount = $em->createQueryBuilder()
            ->select('COUNT(n)')
            ->from(Nominee::class, 'n')
            ->join('n.award', 'a')
            ->where('a.enabled = true')
            ->andWhere('n.flavorText != \'\'')
            ->getQuery()
            ->getSingleScalarResult();

        $imageCount = $em->createQueryBuilder()
            ->select('COUNT(n)')
            ->from(Nominee::class, 'n')
            ->join('n.award', 'a')
            ->where('a.enabled = 1')
            ->andWhere('n.image IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();

        $subtitleCount = $em->createQueryBuilder()
            ->select('COUNT(n)')
            ->from(Nominee::class, 'n')
            ->join('n.award', 'a')
            ->where('a.enabled = true')
            ->andWhere('n.subtitle != \'\'')
            ->getQuery()
            ->getSingleScalarResult();

        $totalNominees = $em->createQueryBuilder()
            ->select('COUNT(n)')
            ->from(Nominee::class, 'n')
            ->join('n.award', 'a')
            ->where('a.enabled = true')
            ->getQuery()
            ->getSingleScalarResult();

        $tasks = [
            'Nominee subtitles' => [$subtitleCount, $totalNominees],
            'Nominee flavour text' => [$flavourTextCount, $totalNominees],
            'Nominee images' => [$imageCount, $totalNominees],
        ];

        foreach ($tasks as $name => $raw) {
            $data = [
                'count' => $raw[0],
                'total' => $raw[1],
                'percent' => $raw[0] / $raw[1] * 100,
            ];

            if ($data['percent'] < 50) {
                $data['class'] = 'danger';
            } elseif ($data['percent'] < 90) {
                $data['class'] = 'warning';
            } else {
                $data['class'] = 'success';
            }

            $tasks[$name] = $data;
        }

        return $this->render('tasks.html.twig', [
            'title' => 'Tasks',
            'tasks' => $tasks
        ]);
    }
}
