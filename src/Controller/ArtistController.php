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
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Filesystem\Filesystem;


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

    #[Route('/{id}', name: 'details', requirements: ['id' => '\d+'])]
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

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, ArtistRepository $artistRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $artist = $artistRepository->find($id);
        if (!$artist) {
            throw $this->createNotFoundException('Artiste non trouvé pour l\'ID ' . $id);
        }
        $form = $this->createForm(ArtistFormType::class, $artist);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $oldImage = $artist->getImage();
                $artistPicturesDirectory = $this->getParameter('artist_pictures_directory');

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move($artistPicturesDirectory, $newFilename);
                    if ($oldImage && file_exists($artistPicturesDirectory . '/' . $oldImage)) {
                        $filesystem = new Filesystem();
                        $filesystem->remove($artistPicturesDirectory . '/' . $oldImage);
                    }
                    $artist->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de la gestion de l\'image : ' . $e->getMessage());
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_artist_details', ['id' => $artist->getId()]);
        }

        return $this->render('artists/edit.html.twig', [
            'artist' => $artist,
            'form' => $form->createView(),
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