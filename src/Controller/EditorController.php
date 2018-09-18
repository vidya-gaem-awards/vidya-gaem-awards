<?php
namespace App\Controller;

use App\Entity\Action;
use App\Entity\TableHistory;
use App\Entity\Template;
use App\Service\AuditService;
use App\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EditorController extends Controller
{
    public function indexAction(EntityManagerInterface $em, Request $request)
    {
        $templateId = $request->query->get('template');

        if ($templateId) {
            $template = $em->getRepository(Template::class)->findOneBy(['filename' => $templateId]);
            if (!$template) {
                $this->addFlash('error', 'Invalid template specified.');
                return $this->redirectToRoute('editor');
            }
        } else {
            $template = null;
        }

        $templates = $em->getRepository(Template::class)->findAll();

        return $this->render('editor.html.twig', [
            'templates' => $templates,
            'template' => $template
        ]);
    }

    public function postAction(EntityManagerInterface $em, ConfigService $config, Request $request, AuditService $auditService)
    {
        $post = $request->request;
        $templateName = $post->get('template');
        $returnRouteParams = ['template' => $templateName];

        if ($config->isReadOnly()) {
            $this->addFlash('error', 'The site is currently in read-only mode. No changes can be made.');
            return $this->redirectToRoute('editor', $returnRouteParams);
        }

        $template = $em->getRepository(Template::class)->findOneBy(['filename' => $templateName]);
        if (!$template) {
            $this->addFlash('error', 'Invalid template specified.');
            return $this->redirectToRoute('editor', $returnRouteParams);
        }

        $content = $post->get('codeMirror');
        $content = str_replace("\r\n", "\n", $content);

        if ($content !== $template->getSource()) {
            $template->setSource($content);
            $template->setLastUpdated(new \DateTime());
            $em->persist($template);
            $em->flush();

            $auditService->add(
                new Action('template-edited', $template->getId()),
                new TableHistory(Template::class, $template->getId(), ['source' => $content])
            );
        }

        $this->addFlash('success', 'Your changes have been saved.');
        return $this->redirectToRoute('editor', $returnRouteParams);
    }
}
