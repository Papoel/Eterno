<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        $lights = [];

        // 1. Je récupère tous les objets Light de l'utilisateur connecté
        if (null !== $user) {
            $lights = $user->getLights()->toArray();
        }

        return $this->render(view: 'home/index.html.twig', parameters: [
            'lights' => $lights,
        ]);
    }
}
