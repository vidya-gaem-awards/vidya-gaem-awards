<?php
namespace App\Controller;

use App\Entity\Advertisement;
use App\Entity\InventoryItem;
use App\Entity\User;
use App\Service\AuditService;
use App\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use App\Entity\Action;
use App\Entity\Award;
use App\Entity\Config;
use App\Entity\Nominee;
use App\Entity\Vote;
use App\Entity\VotingCodeLog;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class VotingController extends AbstractController
{
    public function indexAction(?string $awardID, EntityManagerInterface $em, ConfigService $configService, Request $request, AuthorizationCheckerInterface $authChecker, UserInterface $user, SessionInterface $session)
    {
        /** @var User $user */

        /** @var Award[] $awards */
        $awards = $em->createQueryBuilder()
            ->select('a')
            ->from(Award::class, 'a', 'a.id')
            ->where('a.enabled = true')
            ->orderBy('a.order', 'ASC')
            ->getQuery()
            ->getResult();

        $prevAward = null;
        $nextAward = null;
        $voteJSON = [null];

        $config = $configService->getConfig();

        $start = $config->getVotingStart();
        $end = $config->getVotingEnd();

        $votingNotYetOpen = $config->isVotingNotYetOpen();
        $votingClosed = $config->hasVotingClosed();
        $votingOpen = $config->isVotingOpen();

        if ($votingNotYetOpen) {
            if (!$start) {
                $voteText = 'Voting will open soon.';
            } else {
                $voteText = 'Voting will open in ' . Config::getRelativeTimeString($start) . '.';
            }
        } elseif ($votingOpen) {
            if (!$end) {
                $voteText = 'Voting is now open!';
            } else {
                $voteText = 'You have ' . Config::getRelativeTimeString($end) . ' left to vote.';
            }
        } else {
            $voteText = 'Voting is now closed.';
        }

        // Users with special access to the voting page can change the current vote status for testing purposes
        if ($authChecker->isGranted('ROLE_VOTING_VIEW')) {
            $time = $request->get('time');
            if ($time === 'before') {
                $votingNotYetOpen = true;
                $votingOpen = $votingClosed = false;
                $voteText = 'Voting will open soon.';
            } elseif ($time === 'after') {
                $votingClosed = true;
                $votingNotYetOpen = $votingOpen = false;
                $voteText = 'Voting is now closed.';
            } elseif ($time === 'during') {
                $votingOpen = true;
                $votingNotYetOpen = $votingClosed = false;
                $voteText = 'Voting is now open!';
            }
        }

        /** @var Vote[] $votes */
        $votes = $em->createQueryBuilder()
            ->select('v')
            ->from(Vote::class, 'v')
            ->where('v.cookieID = :cookie')
            ->setParameter('cookie', $user->getRandomID())
            ->getQuery()
            ->getResult();

        $simpleVotes = [];
        foreach ($awards as $a) {
            $simpleVotes[$a->getId()] = [];
        }
        foreach ($votes as $vote) {
            $preferences = $vote->getPreferences();
            array_unshift($preferences, null);
            $simpleVotes[$vote->getAward()->getId()] = $preferences;
        }

        // Fetch the active award (if given)
        if ($awardID) {
            $repo = $em->getRepository(Award::class);

            /** @var Award $award */
            $award = $repo->find($awardID);

            if (!$award || !$award->isEnabled()) {
                $this->addFlash('error', 'Invalid award specified.');
                return $this->redirectToRoute('voting');
            }

            // Iterate through the awards list to get the previous and next awards
            $iterAward = reset($awards);
            while ($iterAward !== $award) {
                $prevAward = $iterAward;
                $iterAward = next($awards);
            }

            $nextAward = next($awards);
            if (!$nextAward) {
                $nextAward = reset($awards);
            }

            if (!$prevAward) {
                $prevAward = end($awards);
            }

            if (isset($simpleVotes[$award->getId()])) {
                $voteJSON = $simpleVotes[$award->getId()];
            }
        }

        $voteDialogMapping = [
            6 => 'height2 width3',
            7 => 'height2 width4',
            8 => 'height2 width4',
            9 => 'height2 width5',
            10 => 'height2 width5',
            11 => 'height3 width4',
            12 => 'height3 width4',
            13 => 'height3 width5',
            14 => 'height3 width5',
            15 => 'height3 width5',
        ];

        if ($request->query->get('legacy') == 1) {
            $session->set('legacyVotingPage', true);
        } elseif ($request->query->get('legacy') == 0) {
            $session->set('legacyVotingPage', false);
        }

        // Fake ads
        $adverts = $em->getRepository(Advertisement::class)->findBy(['special' => 0]);

        if (empty($adverts)) {
            $ad1 = $ad2 = false;
        } else {
            $ad1 = $adverts[array_rand($adverts)];
            $ad2 = $adverts[array_rand($adverts)];
        }

        // Lootbox items
        $items = $em->createQueryBuilder()
            ->select('i')
            ->from(InventoryItem::class, 'i')
            ->indexBy('i', 'i.shortName')
            ->getQuery()
            ->getResult();

        $itemChoiceArray = [];
        /** @var InventoryItem $item */
        foreach ($items as $item) {
            $itemChoiceArray = array_merge($itemChoiceArray, array_fill(0, $item->getRarity(), $item->getShortName()));
        }

        $itemsWithCss = array_filter($items, function (InventoryItem $item) {
            return $item->hasCss() && $item->getCssContents();
        });

        $customCss = '';
        foreach ($itemsWithCss as $item) {
            $customCss .= "/* Start CSS for {$item->getShortName()} */\n";
            $customCss .= $item->getCssContents() . "\n";
            $customCss .= "/* End CSS for {$item->getShortName()} */\n\n";
        }

        $shekelChance = 66; // percent
        $shekelChance = round(1 / ((100 - $shekelChance) / 100) - 1) * 10;

        $itemChoiceArray = array_merge($itemChoiceArray, array_fill(0, count($itemChoiceArray) * $shekelChance, 'shekels'));

        return $this->render('voting.html.twig', [
            'title' => 'Voting',
            'awards' => $awards,
            'award' => $award ?? false,
            'votingNotYetOpen' => $votingNotYetOpen,
            'votingClosed' => $votingClosed,
            'votingOpen' => $votingOpen,
            'voteText' => $voteText,
            'prevAward' => $prevAward,
            'nextAward' => $nextAward,
            'votes' => $voteJSON,
            'allVotes' => $simpleVotes,
            'voteButtonSizeMap' => $voteDialogMapping,
            'votingStyle' => $session->get('legacyVotingPage', false) ? 'legacy' : 'new',
            'ad1' => $ad1,
            'ad2' => $ad2,
            'items' => $items,
            'itemChoiceArray' => $itemChoiceArray,
            'rewardCSS' => $customCss,
        ]);
    }

    public function postAction($awardID, ConfigService $configService, AuthorizationCheckerInterface $authChecker, EntityManagerInterface $em, Request $request, UserInterface $user, AuditService $auditService)
    {
        if ($configService->isReadOnly()) {
            return $this->json(['error' => 'Voting has closed.']);
        }

        if (!$authChecker->isGranted('ROLE_VOTING_VIEW')) {
            if ($configService->getConfig()->isVotingNotYetOpen()) {
                return $this->json(['error' => 'Voting hasn\'t started yet.']);
            } elseif ($configService->getConfig()->hasVotingClosed()) {
                return $this->json(['error' => 'Voting has closed.']);
            }
        }

        /** @var Award $award */
        $award = $em->getRepository(Award::class)->find($awardID);

        if (!$award || !$award->isEnabled()) {
            return $this->json(['error' => 'Invalid award specified.']);
        }

        $preferences = $request->request->get('preferences', ['']);

        // Remove blank preferences and recreate the key ordering.
        $preferences = array_values(array_filter($preferences));
        // By adding an element to the front and then removing it, we shift the keys from 0 to n to 1 to n+1.
        array_unshift($preferences, '');
        unset($preferences[0]);

        if (count($preferences) != count(array_unique($preferences))) {
            return $this->json(['error' => 'Duplicate nominees are not allowed.']);
        }

        $nomineeIDs = $award->getNominees()->map(function (Nominee $n) {
            return $n->getShortName();
        });
        $invalidNominees = array_diff($preferences, $nomineeIDs->toArray());

        if (count($invalidNominees) > 0) {
            return $this->json(
                ['error' => 'Some of the nominees you\'ve voted for are invalid: ' . implode(', ', $invalidNominees)]
            );
        }

        /** @var User $user */

        $query = $em->createQueryBuilder()
            ->select('v')
            ->from(Vote::class, 'v')
            ->join('v.award', 'a')
            ->where('a.id = :award')
            ->setParameter('award', $award->getId())
            ->andWhere('v.cookieID = :cookie')
            ->setParameter('cookie', $user->getRandomID());

        $vote = $query->getQuery()->getOneOrNullResult();

        if (count($preferences) === 0) {
            if ($vote) {
                $em->remove($vote);
                $em->flush();
            }
            return $this->json(['success' => true]);
        }

        if (!$vote) {
            $vote = new Vote();
            $vote
                ->setAward($award)
                ->setCookieID($user->getRandomID());
        }

        $vote
            ->setPreferences($preferences)
            ->setTimestamp(new \DateTime())
            ->setUser($user)
            ->setIp($user->getIP())
            ->setVotingCode($user->getVotingCode());
        $em->persist($vote);

        $auditService->add(
            new Action('voted', $award->getId())
        );
        $em->flush();

        return $this->json(['success' => true]);
    }

    public function codeEntryAction($code, ConfigService $configService, Request $request, EntityManagerInterface $em, UserInterface $user, SessionInterface $session)
    {
        $session->set('votingCode', $code);

        if (!$configService->isReadOnly()) {
            $log = new VotingCodeLog();
            $log
                ->setUser($user)
                ->setCode($code)
                ->setReferer($request->server->get('HTTP_REFERER'))
                ->setTimestamp(new \DateTime());

            $em->persist($log);
            $em->flush();
        }

        $response = $this->redirectToRoute('voting');
        $response->headers->setCookie(new Cookie(
            'votingCode',
            $code,
            new \DateTime('+90 days'),
            '/',
            $request->getHost()
        ));
        return $response;
    }

    public function codeViewerAction(RouterInterface $router)
    {
        $date = new \DateTimeImmutable(date('Y-m-d H:00'));

        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $code = '';
        for ($i = 0; $i < 4; $i++) {
            $seedString = $this->getParameter('kernel.secret') . $date->format(' Y-m-d H:00 ') . $i;
            $code .= $characters[self::randomNumber($seedString, strlen($characters) - 1)];
        }

        $url = $router->generate('voteWithCode', ['code' => $code], UrlGenerator::ABSOLUTE_URL);
        $url = substr($url, 0, strrpos($url, '/') + 1);

        return $this->render('votingCode.html.twig', [
            'title' => 'Voting Code',
            'date' => $date,
            'url' => $url,
            'code' => $code
        ]);
    }

    /**
     * Normally we would just use random_int, but we want to be able to provide a seed.
     * @param string $seed
     * @param int $max
     * @return int
     */
    private static function randomNumber(string $seed, int $max)
    {
        //hash the seed to ensure enough random(ish) characters each time
        $hash = sha1($seed);

        //use the first x characters, and convert from hex to base 10 (this is where the random number is obtain)
        $rand = base_convert(substr($hash, 0, 6), 16, 10);

        //as a decimal percentage (ensures between 0 and max number)
        return (int)round($rand / 0xFFFFFF * $max);
    }
}
