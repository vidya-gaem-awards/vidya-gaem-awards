<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use VGA\Model\Action;
use VGA\Model\Category;
use VGA\Model\Config;
use VGA\Model\Nominee;
use VGA\Model\Vote;

class VotingController extends BaseController
{
    public function indexAction($category = null)
    {
        $tpl = $this->twig->loadTemplate('voting.twig');

        // Fetch all of the enabled categories
        $repo = $this->em->getRepository(Category::class);
        $query = $repo->createQueryBuilder('c', 'c.id');
        $query->select('c')
            ->where('c.enabled = true')
            ->orderBy('c.order', 'ASC');
        $categories = $query->getQuery()->getResult();

        $prevCategory = null;
        $nextCategory = null;
        $voteJSON = [null];

        /** @var Config $config */
        $config = $this->em->getRepository(Config::class)->findOneBy([]);

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
            if ($end) {
                $voteText = 'Voting is now open!';
            } else {
                $voteText = ' You have ' . Config::getRelativeTimeString($end) . ' left to vote.';
            }
        } else {
            $voteText = 'Voting is now closed.';
        }

        // Users with special access to the voting page can change the current vote status for testing purposes
        if ($this->user->canDo('voting-view')) {
            $time = $this->request->get('time');
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

        $query = $this->em->getRepository(Vote::class)->createQueryBuilder('v');
        /** @var Vote[] $votes */
        $votes = $query
            ->select('v')
            ->where('v.cookieID = :cookie')
            ->setParameter('cookie', $this->user->getRandomID())
            ->getQuery()
            ->getResult();

        $simpleVotes = [];
        foreach ($votes as $vote) {
            $preferences = $vote->getPreferences();
            array_unshift($preferences, null);
            $simpleVotes[$vote->getCategory()->getId()] = $preferences;
        }

        // Fetch the active category (if given)
        if ($category) {
            $repo = $this->em->getRepository(Category::class);

            /** @var Category $category */
            $category = $repo->find($category);

            if (!$category || !$category->isEnabled()) {
                $this->session->getFlashBag()->add('error', 'Invalid award specified.');
                $response = new RedirectResponse(
                    $this->generator->generate('voting', [], UrlGenerator::ABSOLUTE_URL)
                );
                $response->send();
                return;
            }

            // Iterate through the categories list to get the previous and next categories
            $iterCategory = reset($categories);
            while ($iterCategory !== $category) {
                $prevCategory = $iterCategory;
                $iterCategory = next($categories);
            }

            $nextCategory = next($categories);
            if (!$nextCategory) {
                $nextCategory = reset($categories);
            }

            if (!$prevCategory) {
                $prevCategory = end($categories);
            }

            if (isset($simpleVotes[$category->getId()])) {
                $voteJSON = $simpleVotes[$category->getId()];
            }
        }

        $response = new Response($tpl->render([
            'title' => 'Voting',
            'categories' => $categories,
            'category' => $category,
            'votingNotYetOpen' => $votingNotYetOpen,
            'votingClosed' => $votingClosed,
            'votingOpen' => $votingOpen,
            'voteText' => $voteText,
            'prevCategory' => $prevCategory,
            'nextCategory' => $nextCategory,
            'votes' => $voteJSON,
            'allVotes' => $simpleVotes
        ]));
        $response->send();
    }

    public function postAction($category)
    {
        $response = new JsonResponse();

        /** @var Config $config */
        $config = $this->em->getRepository(Config::class)->findOneBy([]);

        if (!$this->user->canDo('voting-view')) {
            if ($config->isVotingNotYetOpen()) {
                $response->setData(['error' => 'Voting hasn\'t started yet.']);
                $response->send();
                return;
            } elseif ($config->hasVotingClosed()) {
                $response->setData(['error' => 'Voting has closed.']);
                $response->send();
                return;
            }
        }

        /** @var Category $category */
        $category = $this->em->getRepository(Category::class)->find($category);

        if (!$category || !$category->isEnabled()) {
            $response->setData(['error' => 'Invalid award specified.']);
            $response->send();
            return;
        }

        $preferences = $this->request->request->get('preferences', ['']);

        // Remove blank preferences and recreate the key ordering.
        $preferences = array_values(array_filter($preferences));
        // By adding an element to the front and then removing it, we shift the keys from 0 to n to 1 to n+1.
        array_unshift($preferences, '');
        unset($preferences[0]);

        if (count($preferences) != count(array_unique($preferences))) {
            $response->setData(['error' => 'Duplicate nominees are not allowed.']);
            $response->send();
            return;
        }

        $nomineeIDs = $category->getNominees()->map(function (Nominee $n) {
            return $n->getShortName();
        });
        $invalidNominees = array_diff($preferences, $nomineeIDs->toArray());

        if (count($invalidNominees) > 0) {
            $response->setData(
                ['error' => 'Some of the nominees you\'ve voted for are invalid: ' . implode(', ', $invalidNominees)]
            );
            $response->send();
            return;
        }

        $query = $this->em->getRepository(Vote::class)->createQueryBuilder('v');
        $query
            ->select('v')
            ->join('v.category', 'c')
            ->where('c.id = :category')
            ->setParameter('category', $category->getId())
            ->andWhere('v.cookieID = :cookie')
            ->setParameter('cookie', $this->user->getRandomID());

        $vote = $query->getQuery()->getOneOrNullResult();

        if (count($preferences) === 0) {
            if ($vote) {
                $this->em->remove($vote);
                $this->em->flush();
            }
            $response->setData(['success' => true]);
            $response->send();
            return;
        }

        if (!$vote) {
            $vote = new Vote();
            $vote
                ->setCategory($category)
                ->setCookieID($this->user->getRandomID());
        }

        $vote
            ->setPreferences($preferences)
            ->setTimestamp(new \DateTime())
            ->setUser($this->user)
            ->setIp($this->user->getIP())
            ->setVotingCode($this->user->getVotingCode());
        $this->em->persist($vote);

        $action = new Action('voted');
        $action->setUser($this->user)
            ->setPage(__CLASS__)
            ->setData1($category->getId());
        $this->em->persist($action);

        $this->em->flush();

        $response->setData(['success' => true]);
        $response->send();
    }

    public function codeEntryAction($code) {
        // Bad practice, should be using Symfony's request class
        setcookie('votingCode', $code, strtotime('+90 days'), '/', DOMAIN);
        $this->session->set('votingCode', $code);

        $response = new RedirectResponse($this->generator->generate('voting'));
        $response->send();
    }
}
