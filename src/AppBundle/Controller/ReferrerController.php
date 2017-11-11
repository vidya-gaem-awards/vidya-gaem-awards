<?php
namespace AppBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Access;

class ReferrerController extends Controller
{
    public function indexAction(EntityManagerInterface $em)
    {
        $result = $em->createQueryBuilder()
            ->select('MAX(a.timestamp) as latest')
            ->from(Access::class, 'a')
            ->addSelect('COUNT(a.id) as total')
            ->addSelect('a.referer')
            ->where("a.referer NOT LIKE '%vidyagaemawards.com%'")
            ->andWhere('a.timestamp > :timeLimit')
            ->setParameter('timeLimit', (new \DateTime('-7 days'))->format('Y-m-d H:i:s'))
            ->groupBy('a.referer')
            ->having('total >= 1')
            ->orderBy('total', 'DESC')
            ->addOrderBy('latest', 'DESC')
            ->getQuery()
            ->getArrayResult();

        $referrers = [];

        // Due to the magic of the internet, multiple URLs can resolve to one website.
        // Here we try and combine those URLs as much as possible to get more accurate data.
        foreach ($result as $referer) {
            $referer['latest'] = new \DateTime($referer['latest']);

            // Remove the http and www prefixes, as well as the trailing slash
            $key = rtrim(preg_replace('{https?://(www\.)?}', '', $referer['referer']), '/');

            // Remove the slugs from 4chan threads
            if (preg_match('{boards\.4chan\.org/.+/thread/[0-9]+/.+$}', $key)) {
                $key = preg_replace('{/([0-9]+)/.+?$}', '/$1', $key);
            }

            // Remove the board page number (such as boards.4chan.org/v/3)
            if (preg_match('#boards\.4chan\.org/[^/]+/[0-9]{1,2}#', $key)) {
                $key = preg_replace('{/[0-9]+$}', '', $key);
            }

            if (substr($key, 0, 16) === 'boards.4chan.org') {
                $class = 'success';
            } elseif (substr($key, 0, 10) === 'reddit.com') {
                $class = 'danger';
            } else {
                $class = 'warning';
            }
            $referer['class'] = $class;

            if (!isset($referrers[$key])) {
                $referrers[$key] = $referer;
            } else {
                $referrers[$key]['total'] += $referer['total'];
                $referrers[$key]['latest'] = max($referer['latest'], $referrers[$key]['latest']);
            }
        }

        // We may have to redo the sort after combining some of the referers
        usort($referrers, function ($a, $b) {
            $total = $b['total'] <=> $a['total'];
            if ($total !== 0) {
                return $total;
            }
            return $b['latest'] <=> $a['latest'];
        });

        return $this->render('referrers.html.twig', [
            'title' => 'Referrers',
            'referrers' => $referrers
        ]);
    }
}
