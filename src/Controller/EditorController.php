<?php
namespace App\Controller;

use App\Entity\Action;
use App\Entity\TableHistory;
use App\Entity\Template;
use App\Service\AuditService;
use App\Service\ConfigService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Source;

class EditorController extends AbstractController
{
    public function indexAction(EntityManagerInterface $em, Request $request): Response
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
            'source' => $request->request->get('codeMirror'),
            'available' => $this->getParameter('dynamic_templates')
        ]);
    }

    public function postAction(EntityManagerInterface $em, ConfigService $config, Request $request, AuditService $auditService, Environment $twig, KernelInterface $kernel): Response
    {
        $post = $request->request;
        $templateName = $post->get('template');
        $returnRouteParams = ['template' => $templateName];

        if ($config->isReadOnly()) {
            $this->addFlash('error', 'The site is currently in read-only mode. No changes can be made.');
            return $this->redirectToRoute('editor', $returnRouteParams);
        }

        if (!$this->getParameter('dynamic_templates')) {
            $this->addFlash('error', 'Dynamic templates are not enabled in the site backend.');
            return $this->redirectToRoute('editor');
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
            $template->setLastUpdated(new DateTime());
            $em->persist($template);
            $em->flush();

            $auditService->add(
                new Action('template-edited', $template->getId()),
                new TableHistory(Template::class, $template->getId(), ['source' => $content])
            );
        }

        $filesystem = new Filesystem();
        $filesystem->remove($kernel->getCacheDir() . '/twig');

        $this->addFlash('success', 'Your changes have been saved.');
        return $this->redirectToRoute('editor', $returnRouteParams);
    }
}
