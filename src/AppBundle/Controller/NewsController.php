<?php
namespace AppBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\News;

class NewsController extends Controller
{
    public function indexAction(EntityManagerInterface $em)
    {
        $repo = $em->getRepository(News::class);
        $query = $repo->createQueryBuilder('n');
        $query->select('n, u')
            ->join('n.user', 'u')
            ->where('n.visible = true')
            ->orderBy('n.timestamp', 'DESC');

//        if (!$this->user->canDo('news-manage')) {
//            $query->andWhere('n.timestamp < CURRENT_TIMESTAMP()');
//        }

        $news = $query->getQuery()->getResult();

        return $this->render('news.twig', [
            'title' => 'News',
            'news' => $news
        ]);
    }

    public function addAction()
    {
        if ($this->config->isReadOnly()) {
            $this->session->getFlashBag()->add('error', 'The site is currently in read-only mode. No changes can be made.');
            $response = new RedirectResponse($this->generator->generate('news'));
            $response->send();
            return;
        }

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
        $news->setDeletedBy($this->user);

        $this->em->persist($news);
        $this->em->flush();

        $this->session->getFlashBag()->add('success', 'News item successfully deleted.');
        $response = new RedirectResponse($this->generator->generate('news'));
        $response->send();
    }
}
