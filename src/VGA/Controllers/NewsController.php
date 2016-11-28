<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use VGA\Model\News;

class NewsController extends BaseController
{
    public function indexAction()
    {
        $repo = $this->em->getRepository(News::class);
        $query = $repo->createQueryBuilder('n');
        $query->select('n')
            ->where('n.visible = true')
            ->orderBy('n.timestamp', 'DESC');

        if (!$this->user->canDo('news-manage')) {
            $query->andWhere('n.timestamp < CURRENT_TIMESTAMP()');
        }

        $news = $query->getQuery()->getResult();

        $tpl = $this->twig->loadTemplate('news.twig');

        $response = new Response($tpl->render([
            'title' => 'News',
            'news' => $news
        ]));
        $response->send();
    }

    public function addAction()
    {
        $post = $this->request->request;

        if (!$post->get('news_text')) {
            $this->session->getFlashBag()->add('error', 'Cannot add a news item without any text.');
            $response = new RedirectResponse($this->generator->generate('news'));
            $response->send();
            return;
        } else {
            try {
                $date = new \DateTime($post->get('date'));
            } catch (\Exception $e) {
                $this->session->getFlashBag()->add('error', 'Invalid date provided.');
                $response = new RedirectResponse($this->generator->generate('news'));
                $response->send();
                return;
            }
        }

        $news = new News();
        $news
            ->setText($post->get('news_text'))
            ->setTimestamp($date)
            ->setUser($this->user);

        $this->em->persist($news);
        $this->em->flush();

        $this->session->getFlashBag()->add('success', 'News item successfully added.');
        $response = new RedirectResponse($this->generator->generate('news'));
        $response->send();
    }

    public function deleteAction($id)
    {
        /** @var News $news */
        $news = $this->em->getRepository(News::class)->find($id);

        if (!$news) {
            $this->session->getFlashBag()->add('success', 'Couldn\'t delete news item: invalid ID.');
            $response = new RedirectResponse($this->generator->generate('news'));
            $response->send();
        }

        $news->setVisible(false);

        $this->em->persist($news);
        $this->em->flush();

        $this->session->getFlashBag()->add('success', 'News item successfully deleted.');
        $response = new RedirectResponse($this->generator->generate('news'));
        $response->send();
    }
}
