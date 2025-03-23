<?php

namespace App\Controller\API;

use App\Repository\ArtistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/artist', name: 'api_artist_')]
final class ArtistAPIController extends AbstractController
{
    private ArtistRepository $artistRepository;

    public function __construct(ArtistRepository $artistRepository)
    {
        $this->artistRepository = $artistRepository;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $artists = $this->artistRepository->findAll();

        return $this->json($artists, 200, [], ['groups' => 'artist:read']);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $artist = $this->artistRepository->find($id);

        if (!$artist) {
            return $this->json(['error' => 'Artist not found'], 404);
        }

        return $this->json($artist, 200, [], ['groups' => 'artist:read']);
    }
}
