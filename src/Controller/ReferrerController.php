<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Access;
use Symfony\Component\HttpFoundation\Request;

class ReferrerController extends AbstractController
{
    public function indexAction(EntityManagerInterface $em, Request $request)
    {
        $days = $request->query->get('days');
        if (!ctype_digit($days)) {
            $days = 7;
        }

        $query = $em->createQueryBuilder()
            ->select('MAX(a.timestamp) as latest')
            ->from(Access::class, 'a')
            ->addSelect('COUNT(a.id) as total')
            ->addSelect('a.referer')
            ->where("a.referer NOT LIKE '%vidyagaemawards.com%'");

        if ($days) {
            $date = new \DateTime('-' . $days . ' days');
            $query
                ->andWhere('a.timestamp > :timeLimit')
                ->setParameter('timeLimit', $date->format('Y-m-d H:i:s'));
        }

        $result = $query
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
            $referer['referer'] = $this->cleanUrlPreDisplay($referer['referer']);
            $referer['type'] = false;
            $key = $this->cleanUrlPreCompare($referer['referer']);

            if (substr($key, 0, 14) === 'android-app://') {
                $class = 'info';
                $referer['referer'] = str_replace('android-app://', '', $referer['referer']);
                $referer['type'] = 'android';
            } elseif (substr($key, 0, 16) === 'boards.4chan.org') {
                $class = 'success';
            } elseif (substr($key, 0, 10) === 'reddit.com') {
                $class = 'danger';
            } else {
                $class = 'warning';
            }

            if (substr($key, 0, 5) === 't.co/') {
                $referer['type'] = 'twitter';
            } elseif (substr($key, 0, 15) === 'discordapp.com/') {
                $referer['type'] = 'discord';
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
            'referrers' => $referrers,
            'days' => $days,
        ]);
    }

    private function cleanUrlPreDisplay($referrer)
    {
        // Combine all regional Google domains
        if (preg_match('{^https?://www\.google\.}', $referrer)) {
            $referrer = preg_replace('{www\.google\.[a-z]{2,3}(\.[a-z]{2,3})?/}', 'www.google.com/', $referrer);
        }

        // Remove everything after the search parameter for Google and Bing
        if (preg_match('{search\?q=.+}', $referrer)) {
            $referrer = preg_replace('{search\?q=(.+?)&.+}', 'search?q=$1', $referrer);
        }

        // Strip any parameters off the end of reddit URLs
        if (preg_match('{reddit.com/r/(.+?)/comments}', $referrer)) {
            $referrer = preg_replace('{/comments/(.+)/\?.*}', '/comments/$1/', $referrer);
        }

        // Remove the nonsense from Yandex
        if (preg_match('{yandex\.ru/clck/jsredir}', $referrer)) {
            $referrer = preg_replace('{jsredir\?.*}', 'jsredir', $referrer);
        }

        // Remove the nonsense from Google
        if (preg_match('{www\.google\.(.+)/url?.+}', $referrer)) {
            $referrer = preg_replace('{/url?.+}', '', $referrer);
        }

        // Remove unnecessary URL parameters in SomethingAwful URLs
        if (preg_match('{forums\.somethingawful\.com}', $referrer)) {
            $referrer = preg_replace('{&perpage=\d+}', '', $referrer);
            $referrer = preg_replace('{&userid=\d+}', '', $referrer);
        }

        return $referrer;
    }

    private function cleanUrlPreCompare($referrer)
    {
        // Remove the http and www prefixes, as well as the trailing slash
        $referrer = rtrim(preg_replace('{https?://(www\.)?}', '', $referrer), '/');

        // Replace 4channel.org with 4chan.org
        $referrer = str_replace('4channel.org', '4chan.org', $referrer);

        // Remove the slugs from 4chan threads
        if (preg_match('{boards\.4chan\.org/.+/thread/[0-9]+/.+$}', $referrer)) {
            $referrer = preg_replace('{/([0-9]+)/.+?$}', '/$1', $referrer);
        }

        // Remove the board page number (such as boards.4chan.org/v/3)
        if (preg_match('#boards\.4chan\.org/[^/]+/[0-9]{1,2}#', $referrer)) {
            $referrer = preg_replace('{/[0-9]+$}', '', $referrer);
        }

        // Remove links to individual posts from the end of Facepunch URLs
        if (preg_match('{showthread\.php\?t=\d+&p=.+}', $referrer)) {
            $referrer = preg_replace('{&p=.+}', '', $referrer);
        }

        return $referrer;
    }
}
