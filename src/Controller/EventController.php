<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/events', name: 'app_event_')]
class EventController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager->getRepository(Event::class)->findAll();

        return $this->render('Events/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/events/{id}', name: 'details')]
    public function show(int $id, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Event non trouvé pour l\'ID ' . $id);
        }

        return $this->render('events/details.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $organizer = $this->getUser();

            $event->setOrganizer($organizer);
            $event->addParticipant($organizer);
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('event_pictures_directory'),
                        $newFilename
                    );
                } catch (Exception) {
                }

                $event->setImage($newFilename);
                $entityManager->persist($event);
                $entityManager->flush();
            }


            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('events/new.html.twig', [
            'EventForm' => $form->createView(),
        ]);
    }
}