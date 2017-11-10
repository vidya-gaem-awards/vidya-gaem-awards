<?php
namespace AppBundle\Controller;

use AppBundle\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\News;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class NewsController extends Controller
{
    public function indexAction(EntityManagerInterface $em, AuthorizationCheckerInterface $authChecker)
    {
        $query = $em->createQueryBuilder()
            ->select('n, u')
            ->from(News::class, 'n')
            ->join('n.user', 'u')
            ->where('n.visible = true')
            ->orderBy('n.timestamp', 'DESC');

        if (!$authChecker->isGranted('ROLE_NEWS_MANAGE')) {
            $query->andWhere('n.timestamp < CURRENT_TIMESTAMP()');
        }

        $news = $query->getQuery()->getResult();

        return $this->render('news.html.twig', [
            'title' => 'News',
            'news' => $news
        ]);
    }

    public function addAction(EntityManagerInterface $em, ConfigService $configService, Request $request, UserInterface $user)
    {
        if ($configService->isReadOnly()) {
            $this->addFlash('error', 'The site is currently in read-only mode. No changes can be made.');
            return $this->redirectToRoute('news');
        }

        $post = $request->request;

        if (!$post->get('news_text')) {
            $this->addFlash('error', 'Cannot add a news item without any text.');
            return $this->redirectToRoute('news');
        } else {
            try {
                $date = new \DateTime($post->get('date'));
            } catch (\Exception $e) {
                $this->addFlash('error', 'Invalid date provided.');
                return $this->redirectToRoute('news');
            }
        }

        $news = new News();
        $news
            ->setText($post->get('news_text'))
            ->setTimestamp($date)
            ->setUser($user);

        $em->persist($news);
        $em->flush();

        $this->addFlash('success', 'News item successfully added.');
        return $this->redirectToRoute('news');
    }

    public function deleteAction(int $id, EntityManagerInterface $em, UserInterface $user)
    {
        /** @var News $news */
        $news = $em->getRepository(News::class)->find($id);

        if (!$news) {
            $this->addFlash('success', 'Couldn\'t delete news item: invalid ID.');
            return $this->redirectToRoute('news');
        }

        $news->setVisible(false);
        $news->setDeletedBy($user);

        $em->persist($news);
        $em->flush();

        $this->addFlash('success', 'News item successfully deleted.');
        return $this->redirectToRoute('news');
    }
}
