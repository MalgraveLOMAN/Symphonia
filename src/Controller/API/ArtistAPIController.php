<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/artist', name: 'api_artist_')]
final class ArtistAPIController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $artists = [
            ['id' => 1, 'name' => 'Artist One'],
            ['id' => 2, 'name' => 'Artist Two'],
        ];

        return $this->json($artists);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $artists = [
            1 => ['id' => 1, 'name' => 'Artist One'],
            2 => ['id' => 2, 'name' => 'Artist Two'],
        ];

        if (!isset($artists[$id])) {
            return $this->json(['error' => 'Artist not found'], 404);
        }

        return $this->json($artists[$id]);
    }
}
