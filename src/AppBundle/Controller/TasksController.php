<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Nominee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TasksController extends Controller
{
    public function indexAction(EntityManagerInterface $em)
    {
        $query = $em->createQueryBuilder()
            ->select('n')
            ->from(Nominee::class, 'n')
            ->join('n.award', 'a')
            ->where('a.enabled = 1')
            ->addOrderBy('a.order', 'ASC')
            ->addOrderBy('n.id', 'ASC');

        $flavourText = (clone $query)
            ->andWhere('n.flavorText = \'\'')
            ->getQuery()
            ->getResult();

        $images = (clone $query)
            ->andWhere('n.image IS NULL')
            ->getQuery()
            ->getResult();

        $subtitles = (clone $query)
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
            'Missing subtitles' => [$subtitles, $totalNominees],
            'Flavor text needed' => [$flavourText, $totalNominees],
            'Nominee images needed' => [$images, $totalNominees],
        ];

        foreach ($tasks as $name => $raw) {
            $data = [
                'id' => str_replace(' ', '-', strtolower($name)),
                'count' => count($raw[0]),
                'total' => $raw[1]
            ];
            $data['percent'] = $data['count'] / $data['total'] * 100;

            if ($data['percent'] < 10) {
                $data['class'] = 'success';
            } elseif ($data['percent'] < 50) {
                $data['class'] = 'warning';
            } else {
                $data['class'] = 'danger';
            }

            $data['awards'] = [];

            /** @var Nominee $nominee */
            foreach ($raw[0] as $nominee) {
                $award = $nominee->getAward();
                if (!isset($data['awards'][$award->getId()])) {
                    $data['awards'][$award->getId()] = [
                        'award' => $award,
                        'nominees' => [],
                    ];
                }
                $data['awards'][$award->getId()]['nominees'][] = $nominee;
            }

            $tasks[$name] = $data;
        }

        return $this->render('tasks.html.twig', [
            'title' => 'Tasks',
            'tasks' => $tasks
        ]);
    }
}
