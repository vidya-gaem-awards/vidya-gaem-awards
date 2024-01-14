<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\BaseUser;
use App\Entity\RpgCharacter;
use App\Entity\Vote;
use App\Service\AuditService;
use App\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RpgController extends AbstractController
{
    public function nameAction(
        Request $request,
        ConfigService $configService,
        AuditService $auditService,
        UserInterface $user,
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authChecker,
    ): JsonResponse {
        if ($configService->isReadOnly()) {
            return $this->json(['error' => 'Voting has closed.']);
        }

        if (!$authChecker->isGranted('ROLE_VOTING_VIEW')) {
            if ($configService->getConfig()->isVotingNotYetOpen()) {
                return $this->json(['error' => 'Voting hasn\'t started yet.']);
            } elseif ($configService->getConfig()->hasVotingClosed()) {
                return $this->json(['error' => 'Voting has closed.']);
            }
        }

        $name = trim($request->request->get('name', ''));

        if (empty($name)) {
            return $this->json(['error' => 'Invalid name.']);
        }

        /** @var BaseUser $user */

        $character = new RpgCharacter();
        $character
            ->setName($name)
            ->setTimestamp(new \DateTimeImmutable())
            ->setUser($user)
            ->setCookieID($user->getRandomID());

        $em->persist($character);

        $auditService->add(
            new Action('rpg-entered-name')
        );
        $em->flush();

        return $this->json([]);
    }
}
