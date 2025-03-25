<?php

namespace App\Controller\API;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/event', name: 'api_event_')]
final class EventAPIController extends AbstractController
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste de tous les événements',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Event'),
            example: [
                [
                    'id' => 0,
                    'name' => 'Event Name',
                    'date' => 'DateType',
                    'description' => 'Event Description',
                    'location' => 'Event Location',
                    'image' => null,
                    'organizer' => [],
                    'artists' => [],
                    'participants' => [[]],
                ],
            ]
        )
    )]
    #[OA\Tag(name: 'Events')]
    public function list(): JsonResponse
    {
        $events = $this->eventRepository->findAll();
        return $this->json($events, 200, [], ['groups' => 'event:read']);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: "Retourne les détails d'un événement spécifique",
        content: new OA\JsonContent(
            ref: '#/components/schemas/Event',
            example: [
                'id' => 0,
                'name' => 'Event Name',
                'date' => 'DateType',
                'description' => 'Event Description',
                'location' => 'Event Location',
                'image' => null,
                'organizer' => [],
                'artists' => [],
                'participants' => [[]],
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Événement non trouvé"
    )]
    #[OA\Parameter(
        name: 'id',
        description: "ID de l'événement",
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'Events')]
    public function detail(int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);
        if (!$event) {
            return $this->json(['error' => 'Event not found'], 404);
        }
        return $this->json($event, 200, [], ['groups' => 'event:read']);
    }
}