<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class OregonTrailController extends AbstractController
{
    public function gameDataAction(): JsonResponse
    {
        return $this->json([
            'day' => 3,
            'location' => 'Independence',
            'nextActionTime' => (new \DateTimeImmutable('+90 seconds'))->format('c'),
            'players' => random_int(2, 100),
            'party' => [
                'Alyx Vance',
                'Big the Cat',
                'Dante',
                'PaRappa the Rapper',
                'Abraham Lincoln',
            ],
        ]);
    }
}
