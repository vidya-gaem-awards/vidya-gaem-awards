<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserInventoryItem;
use App\Service\ConfigService;
use App\Service\LootboxService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class InventoryController extends AbstractController
{
    public function purchaseLootbox(ConfigService $configService, LootboxService $lootboxService, UserInterface $user, EntityManagerInterface $em, Request $request): JsonResponse
    {
        /** @var User $user */

        if ($configService->isReadOnly()) {
            return $this->json(['error' => 'The lootbox shop has closed for the year. No refunds!'], 400);
        }

        $rewards = [];
        for ($i = 0; $i < 3; $i++) {
            if (random_int(1, 3) === 3) {
                $rewards[] = ['type' => 'shekels', 'amount' => random_int(2, 1000)];
            } elseif ($item = $lootboxService->getRandomItem()) {
                $rewards[] = ['type' => 'item', 'item' => $item];

                if (!$request->query->get('test')) {
                    $userItem = new UserInventoryItem();
                    $userItem->setUser($user->getFuzzyID());
                    $userItem->setItem($item);
                    $em->persist($userItem);
                }
            } else {
                // Failsafe for if no lootbox items are available
                $rewards[] = ['type' => 'shekels', 'amount' => 1];
            }
        }

        $em->flush();

        return $this->json(['rewards' => $rewards]);
    }
}
