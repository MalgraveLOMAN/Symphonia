<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class SignupController extends AbstractController
{
    private $passwordHasher;
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    #[Route('/signup', name: 'app_signup')]
    public function index(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hacher le mot de passe
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $user->getPassword())
            );

            // Gérer l'upload de l'image (profilePicture)
            $profilePictureFile = $form->get('profilePicture')->getData();
            if ($profilePictureFile) {
                $newFilename = uniqid() . '.' . $profilePictureFile->guessExtension();
                $profilePictureFile->move(
                    $this->getParameter('profile_pictures_directory'),
                    $newFilename
                );
                $user->setProfilePicture($newFilename); // Assure-toi que cette propriété existe dans User
            }

            // Sauvegarder l'utilisateur
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Inscription réussie ! Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_index');
        }

        return $this->render('signup/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}