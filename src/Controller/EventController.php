<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Filesystem\Filesystem;


#[Route('/events', name: 'app_event_')]
class EventController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('date');

        if ($searchTerm) {
            $events = $entityManager->getRepository(Event::class)->findByDate($searchTerm);
        } else {
            $events = $entityManager->getRepository(Event::class)->findAll();
        }


        return $this->render('Events/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/{id}', name: 'details', requirements: ['id' => '\d+'])]
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

    #[Route('/{id}/participate', name: 'participate', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function participate(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {

        $participate = $request->get('participate');

        /* @var User $currentUser */
        $currentUser = $this->getUser();
        if ($participate === '1') {
            if (!$event->getParticipants()->contains($currentUser)) {
                $event->addParticipant($currentUser);
                $entityManager->persist($event);
                $entityManager->flush();
            }
        } elseif ($participate === '0') {
            if ($event->getParticipants()->contains($currentUser)) {
                $event->removeParticipant($currentUser);
                $entityManager->persist($event);
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute('app_event_details', ['id' => $event->getId()]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, EventRepository $eventRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = $eventRepository->find($id);
        if (!$event) {
            throw $this->createNotFoundException('Event non trouvé pour l\'ID ' . $id);
        }
        $currentUser = $this->getUser();
        if ($currentUser !== $event->getOrganizer()) {
            return $this->redirectToRoute('app_event_details', ['id' => $event->getId()]);
        }
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $oldImage = $event->getImage();
                $eventPicturesDirectory = $this->getParameter('event_pictures_directory');
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move($eventPicturesDirectory, $newFilename);
                    if ($oldImage && file_exists($eventPicturesDirectory . '/' . $oldImage)) {
                        $filesystem = new Filesystem();
                        $filesystem->remove($eventPicturesDirectory . '/' . $oldImage);
                    }
                    $event->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de la gestion de l\'image : ' . $e->getMessage());
                }
            }

            $entityManager->persist($event);
            $entityManager->flush();
            return $this->redirectToRoute('app_event_details', ['id' => $event->getId()]);
        }

        return $this->render('events/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(int $id, EventRepository $eventRepository, EntityManagerInterface $entityManager): Response
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Event non trouvé pour l\'ID ' . $id);
        }

        $currentUser = $this->getUser();

        if ($currentUser !== $event->getOrganizer()) {
            return $this->redirectToRoute('app_event_details', ['id' => $event->getId()]);
        }

        $entityManager->remove($event);
        $entityManager->flush();

        return $this->redirectToRoute('app_event_index');
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var $organizer User */
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

            }
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('events/new.html.twig', [
            'EventForm' => $form->createView(),
        ]);
    }
}