<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\ArgCodeInput;
use App\Entity\ArgConfig;
use App\Entity\Autocompleter;
use App\Entity\TableHistory;
use App\Entity\User;
use App\Repository\ArgCodeInputRepository;
use App\Repository\ArgConfigRepository;
use App\Repository\ArgFileRepository;
use App\Service\AuditService;
use App\Service\ConfigService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class ArgController extends AbstractController
{
    public function landingPage()
    {
        return $this->render('argLandingPage.html.twig');
    }

    public function main(ArgFileRepository $repo, ArgConfigRepository $configRepo)
    {
        $files = $repo->findBy([], ['dateVisible' => 'ASC']);
        $now = new DateTimeImmutable();

        $allFilesVisible = false;

        $filesToShow = [];
        foreach ($files as $file) {
            $filesToShow[] = $file;
            if ($file->getDateVisible() >= $now) {
                break;
            }

            if (count($filesToShow) === count($files)) {
                $allFilesVisible = true;
            }
        }

        $config = $configRepo->find(1);
        if (!$config) {
            $config = new ArgConfig();
        }

        return $this->render('argMainPage.html.twig', [
            'files' => $filesToShow,
            'fileCount' => count($files),
            'now' => new DateTimeImmutable(),
            'allFilesVisible' => $allFilesVisible,
            'config' => $config
        ]);
    }

    public function codeInput(Request $request, EntityManagerInterface $em, ConfigService $configService, UserInterface $user, ArgConfigRepository $configRepo)
    {
        $config = $configRepo->find(1);
        if (!$config) {
            $config = new ArgConfig();
        }

        /** @var User $user */

        if ($configService->isReadOnly() || $config->getFinished()) {
            return $this->json(['response' => 'The ARG is over. Thanks for participating!']);
        }

        $code = mb_strtolower($request->request->get('code'));
        if (mb_strlen($code) > 20) {
            return $this->json(['response' => 'Code is too long.']);
        } elseif (!preg_match('/^[a-z0-9]+$/', $code)) {
            return $this->json(['response' => 'Code must be alphanumeric.']);
        }

        // Check if the user has submitted this code themselves

        $result = $em->createQueryBuilder()
            ->select('aci')
            ->from(ArgCodeInput::class, 'aci')
            ->where('aci.user = :fuzzyUser')
            ->andWhere('aci.code = :code')
            ->setParameter('fuzzyUser', $user->getFuzzyID())
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result) {
            return $this->json(['response' => 'Duplicate input detected. Code not processed.']);
        }

        // Check how many other users have submitted this code

        $count = (int)$em->createQueryBuilder()
            ->select('COUNT(aci)')
            ->from(ArgCodeInput::class, 'aci')
            ->where('aci.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getSingleScalarResult();

        $input = new ArgCodeInput();
        $input->setUser($user->getFuzzyID());
        $input->setCode($code);
        $em->persist($input);
        $em->flush();

        $stage = $config->getStage();
        if ($stage === 0) {
            return $this->json(['response' => 'Input received. Multi-user security system is currently in place.']);
        } elseif ($stage === 1) {
            if ($count > 0) {
                return $this->json(['response' => 'Input received. Another user has also entered this code. With enough users, the multi-user security system can be bypassed.']);
            } else {
                return $this->json(['response' => 'Input received. You are the first user to enter this code. Multi-user security system still in place.']);
            }
        } elseif ($stage === 2 || ($stage === 3 && !$config->getNextThreshold())) {
            if ($count === 0) {
                return $this->json(['response' => 'Input received. You are the first user to enter this code. Security systems are preventing single-user input from being validated.']);
            } else {
                return $this->json(['response' => 'Input received. ' . $count . ' other users have entered this code. Threshold for bypassing the multi-user security system is still unknown.']);
            }
        } elseif ($stage === 3) {
            if ($count === 0) {
                return $this->json(['response' => 'Input received. You are the first user to enter this code. Security systems are preventing single-user input from being validated.']);
            } elseif ($count < $config->getNextThreshold()) {
                return $this->json(['response' => 'Input received. ' . $count . ' other users have entered this code. Security bypass is underway - a total of ' . $config->getNextThreshold() . ' users must provide this input.']);
            } else {
                return $this->json(['response' => 'Input received. The threshold for bypassing the multi-user security system has been breached. Code verificiation is imminent.']);
            }
        }

        return $this->json(['response' => 'This should never happen. Contact a developer. (This error is not part of the ARG)']);
    }

    public function backend(ArgCodeInputRepository $repo, ArgFileRepository $fileRepo, ArgConfigRepository $configRepo)
    {
        $codes = $repo->findBy([], ['timestamp' => 'DESC']);

        $codeCount = [];
        foreach ($codes as $code) {
            if (!isset($codeCount[$code->getCode()])) {
                $codeCount[$code->getCode()] = 0;
            }

            $codeCount[$code->getCode()]++;
        }

        ksort($codeCount);
        asort($codeCount);

        $files = $fileRepo->findBy([], ['dateVisible' => 'ASC']);

        $config = $configRepo->find(1) ?? new ArgConfig();

        return $this->render('argBackend.html.twig', [
            'codes' => $codeCount,
            'latestCodes' => array_slice($codes, 0, 50),
            'files' => $files,
            'config' => $config
        ]);
    }

    public function backendSaveTimes(ArgFileRepository $fileRepo, Request $request, ConfigService $configService, EntityManagerInterface $em, AuditService $auditService)
    {
        if ($configService->isReadOnly()) {
            $this->addFlash('error', 'The site is currently in read-only mode. No changes can be made.');
            return $this->redirectToRoute('argBackend');
        }

        $unlockTimes = $request->request->get('files');

        $count = 1;
        foreach ($unlockTimes as $id => $time) {
            $file = $fileRepo->find($id);

            if (!$file) {
                $this->addFlash('error', 'Invalid ID for file ' . $count . '.');
                return $this->redirectToRoute('argBackend');
            }

            if (empty($time)) {
                $this->addFlash('error', 'Unlock time must be specified for file ' . $count . '.');
                return $this->redirectToRoute('argBackend');
            }

            try {
                $datetime = new DateTimeImmutable($time);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Invalid date specified for file ' . $count . '.');
                return $this->redirectToRoute('argBackend');
            }

            $count++;

            $file->setDateVisible($datetime);
            $em->persist($file);
        }

        $em->flush();

        $auditService->add(
            new Action('arg-times-updated'),
        );

        $this->addFlash('success', 'File unlock times saved.');
        return $this->redirectToRoute('argBackend');
    }

    public function backendSaveConfig(ArgConfigRepository $repo, Request $request, ConfigService $configService, EntityManagerInterface $em, AuditService $auditService)
    {
        if ($configService->isReadOnly()) {
            $this->addFlash('error', 'The site is currently in read-only mode. No changes can be made.');
            return $this->redirectToRoute('argBackend');
        }

        $config = $repo->find(1);
        if (!$config) {
            $config = new ArgConfig();
            $config->setId(1);
        }

        $post = $request->request;

        $codeCount = $post->getInt('codeCount');
        if ($codeCount < 0) {
            $this->addFlash('error', 'Total code count cannot be negative.');
            return $this->redirectToRoute('argBackend');
        } elseif ($codeCount === 0) {
            $codeCount = null;
        }

        $threshold = $post->getInt('nextThreshold');
        if ($threshold < 0) {
            $this->addFlash('error', 'Threshold for next code cannot be negative.');
            return $this->redirectToRoute('argBackend');
        } elseif ($threshold === 0) {
            $threshold = null;
        }

        $codes = explode("\n", $post->get('acceptedCodes'));
        $codes = array_map(fn ($code) => trim($code), $codes);
        $codes = array_filter($codes);

        $config->setAcceptedCodes($codes);
        $config->setCodeCount($codeCount);
        $config->setStage($post->get('stage'));
        $config->setNextThreshold($threshold);
        $config->setFinished($post->getBoolean('finished'));
        $em->persist($config);

        $auditService->add(
            new Action('arg-config-updated', 1),
            new TableHistory(ArgConfig::class, 1, $post->all())
        );

        $em->flush();

        $this->addFlash('success', 'ARG config saved.');
        return $this->redirectToRoute('argBackend');
    }
}
