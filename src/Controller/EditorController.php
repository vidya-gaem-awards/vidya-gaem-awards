<?php
namespace App\Controller;

use App\Entity\Action;
use App\Entity\TableHistory;
use App\Entity\Template;
use App\Service\AuditService;
use App\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Source;

class EditorController extends AbstractController
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
            'template' => $template,
            'source' => $request->request->get('codeMirror')
        ]);
    }

    public function postAction(EntityManagerInterface $em, ConfigService $config, Request $request, AuditService $auditService, Environment $twig)
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

        // Do a Twig syntax check of the source code before saving. This won't prevent all errors, but if provides
        // a basic safety net.
        try {
            $twig->tokenize(new Source($content, $templateName));
        } catch (SyntaxError $e) {
            $this->addFlash('error', 'Syntax error: ' . $e->getMessage());

            $request->query->set('template', $templateName);
            return $this->indexAction($em, $request);
        }

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
