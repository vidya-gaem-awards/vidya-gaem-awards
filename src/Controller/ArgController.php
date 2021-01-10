<?php

namespace App\Controller;

use App\Entity\ArgCodeInput;
use App\Entity\User;
use App\Repository\ArgCodeInputRepository;
use App\Service\AuditService;
use App\Service\ConfigService;
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

    public function main()
    {
        return $this->render('argMainPage.html.twig');
    }

    public function codeInput(Request $request, EntityManagerInterface $em, ConfigService $configService, UserInterface $user, AuditService $auditService)
    {
        /** @var User $user */

        if ($configService->isReadOnly()) {
            return $this->json(['response' => 'The ARG is over. Thanks for participating!']);
        }

        $code = mb_strtolower($request->request->get('code'));
        if (mb_strlen($code) > 20) {
            return $this->json(['response' => 'Code is too long.']);
        }

        $query = $em->getRepository(ArgCodeInput::class)->createQueryBuilder('aci');

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

        $input = new ArgCodeInput();
        $input->setUser($user->getFuzzyID());
        $input->setCode($code);
        $em->persist($input);
        $em->flush();

        return $this->json(['response' => 'Input received. Validation systems not yet active. Additional input required from multiple users.']);
    }

    public function backend(ArgCodeInputRepository $repo)
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

        return $this->render('argBackend.html.twig', [
            'codes' => $codeCount,
            'latestCodes' => array_slice($codes, 0, 50),
        ]);
    }
}
