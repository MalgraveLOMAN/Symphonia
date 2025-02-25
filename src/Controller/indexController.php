<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class indexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        $user = $this->getUser();
        $participatedEvents = $user ? $user->getParticipatedEvents() : [];

        return $this->render('default/index.html.twig', [
            "user" => $user,
            'participatedEvents' => $participatedEvents

        ]);
    }
}
