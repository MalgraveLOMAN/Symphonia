<?php

namespace App\Controller\API;

use App\Repository\ArtistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/artist', name: 'api_artist_')]
final class ArtistAPIController extends AbstractController
{
    private ArtistRepository $artistRepository;

    public function __construct(ArtistRepository $artistRepository)
    {
        $this->artistRepository = $artistRepository;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste de tous les artistes',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Artist'),
            example: [
                [
                    'id' => 0,
                    'name' => 'Artist Name',
                    'description' => 'Artist Description',
                    'image' => '',
                    'events' => [],
                ],
            ]
        )
    )]
    #[OA\Tag(name: 'Artists')]
    public function list(): JsonResponse
    {
        $artists = $this->artistRepository->findAll();
        return $this->json($artists, 200, [], ['groups' => 'artist:read']);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: "Retourne les détails d'un artiste spécifique",
        content: new OA\JsonContent(
            ref: '#/components/schemas/Artist',
            example: [
                'id' => 0,
                'name' => 'Artist Name',
                'description' => 'Artist Description',
                'image' => '',
                'events' => [],
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Artiste non trouvé"
    )]
    #[OA\Parameter(
        name: 'id',
        description: "ID de l'artiste",
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'Artists')]
    public function detail(int $id): JsonResponse
    {
        $artist = $this->artistRepository->find($id);
        if (!$artist) {
            return $this->json(['error' => 'Artist not found'], 404);
        }
        return $this->json($artist, 200, [], ['groups' => 'artist:read']);
    }
}