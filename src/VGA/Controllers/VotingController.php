<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use VGA\Model\Category;

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

        // Fetch the active category (if given)
        if ($category) {
            /** @var Category $category */
            $category = $this->em->getRepository(Category::class)->find($category);

            if (!$category || !$category->isEnabled()) {
                $this->session->getFlashBag()->add('error', 'Invalid award specified.');
                $response = new RedirectResponse(
                    $this->generator->generate('voting', [], UrlGenerator::ABSOLUTE_URL)
                );
                $response->send();
                return;
            }
        }

        //////// TESTING ////////
        $votingNotYetOpen = $votingEnabled = $votingConcluded = false;

        $time = $this->request->get('time', 'during');
        if ($time === 'before') {
            $votingNotYetOpen = true;
            $voteText = 'Voting will open in 18 hours and 45 minutes.';
        } elseif ($time === 'after') {
            $votingConcluded = true;
            $voteText = 'Voting is now closed.';
        } else {
            $votingEnabled = true;
            $voteText = 'Voting is now open! You have 18 hours and 45 minutes left to vote.';
        }
        //////// TESTING ////////

        $response = new Response($tpl->render([
            'title' => 'Voting',
            'categories' => $categories,
            'category' => $category,
            'votingNotYetOpen' => $votingNotYetOpen,
            'votingConcluded' => $votingConcluded,
            'votingEnabled' => $votingEnabled,
            'voteText' => $voteText
        ]));
        $response->send();
    }
}
