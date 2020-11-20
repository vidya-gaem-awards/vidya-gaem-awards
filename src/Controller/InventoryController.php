<?php

namespace App\Controller;

use App\Service\ConfigService;
use App\Service\LootboxService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InventoryController extends AbstractController
{
    public function purchaseLootbox(ConfigService $configService, LootboxService $lootboxService)
    {
        if ($configService->isReadOnly()) {
            return $this->json(['error' => 'The lootbox shop has closed for the year. No refunds!'], 400);
        }

        $rewards = [];
        for ($i = 0; $i < 3; $i++) {
            if ($i === 0) {
                $rewards[] = ['type' => 'shekels', 'amount' => random_int(2, 1000)];
            } else {
                $item = $lootboxService->getRandomItem();
                $rewards[] = ['type' => 'item', 'id' => $item->getId(), 'shortName' => $item->getShortName()];
            }
        }

        return $this->json(['rewards' => $rewards]);
    }
}
