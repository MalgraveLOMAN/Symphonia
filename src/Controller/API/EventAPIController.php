<?php

namespace App\Controller\API;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/event', name: 'api_event_')]
final class EventAPIController extends AbstractController
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $events = $this->eventRepository->findAll();

        return $this->json($events, 200, [], ['groups' => 'event:read']);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);

        if (!$event) {
            return $this->json(['error' => 'Event not found'], 404);
        }

        return $this->json($event, 200, [], ['groups' => 'event:read']);
    }
}
