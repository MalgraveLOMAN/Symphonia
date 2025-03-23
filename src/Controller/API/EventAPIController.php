<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/event', name: 'api_event_')]
final class EventAPIController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $events = [
            ['id' => 1, 'title' => 'Concert A', 'date' => '2025-06-10'],
            ['id' => 2, 'title' => 'Festival B', 'date' => '2025-07-22'],
        ];

        return $this->json($events);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $events = [
            1 => ['id' => 1, 'title' => 'Concert A', 'date' => '2025-06-10'],
            2 => ['id' => 2, 'title' => 'Festival B', 'date' => '2025-07-22'],
        ];

        if (!isset($events[$id])) {
            return $this->json(['error' => 'Event not found'], 404);
        }

        return $this->json($events[$id]);
    }
}
