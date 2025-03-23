<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/users', name: 'app_users_')]
class MemberController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User non trouvé pour l\'ID ' . $id);
        }

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('profilePicture')->getData();
            if ($imageFile) {
                $oldImage = $user->getProfilePicture();
                $profilePicturesDirectory = $this->getParameter('profile_pictures_directory');
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move($profilePicturesDirectory, $newFilename);
                    if ($oldImage && file_exists($profilePicturesDirectory . '/' . $oldImage)) {
                        $filesystem = new Filesystem();
                        $filesystem->remove($profilePicturesDirectory . '/' . $oldImage);
                    }
                    $user->setProfilePicture($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de la gestion de l\'image : ' . $e->getMessage());
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_users_details', ['id' => $user->getId()]);
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'details')]
    public function show(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('user non trouvé pour l\'ID ' . $id);
        }

        return $this->render('users/details.html.twig', [
            'user' => $user,
        ]);
    }
}
