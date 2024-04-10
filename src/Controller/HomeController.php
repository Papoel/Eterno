<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home.index', methods: ['GET'])]
    public function index(): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        $lights = [];

        // 1. Je récupère tous les objets Light de l'utilisateur connecté
        if (null !== $user) {
            $lights = $user->getLights()->toArray();
        }

        return $this->render(view: 'home/home.html.twig', parameters: [
            'lights' => $lights,
        ]);
    }
}
