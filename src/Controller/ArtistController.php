<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Form\ArtistFormType;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/artists', name: 'app_artist_')]
class ArtistController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $artists = $entityManager->getRepository(Artist::class)->findAll();

        return $this->render('artists/index.html.twig', [
            'artists' => $artists,
        ]);
    }

    #[Route('/artist/{id}', name: 'details')]
    public function show(int $id, ArtistRepository $artistRepository): Response
    {
        $artist = $artistRepository->find($id);

        if (!$artist) {
            throw $this->createNotFoundException('Artiste non trouvé pour l\'ID ' . $id);
        }

        return $this->render('artists/details.html.twig', [
            'artist' => $artist,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $artist = new Artist();
        $form = $this->createForm(ArtistFormType::class, $artist);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('artist_pictures_directory'),
                        $newFilename
                    );
                } catch (Exception) {
                }

                $artist->setImage($newFilename);
            }
            $entityManager->persist($artist);
            $entityManager->flush();
            return $this->redirectToRoute('app_artist_index');
        }

        return $this->render('artists/new.html.twig', [
            'ArtistForm' => $form->createView(),
        ]);
    }
}