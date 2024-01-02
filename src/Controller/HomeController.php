<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(MessageRepository $messageRepository): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        $lights = [];
        $userMessages = [];

        // 1. Je récupère tous les objets Light de l'utilisateur connecté
        if (null !== $user) {
            $lights = $user->getLights()->toArray();
            $lightsIds = [];

            foreach ($lights as $light) {
                $lightId = $light->getId();

                if ($lightId instanceof Uuid) {
                    $lightsIds[] = $lightId->toRfc4122();
                }
            }

            // 2. Je récupère tous les messages envoyés par l'utilisateur connecté
            /** @phpstan-ignore-next-line */
            $userMessages = $messageRepository->findMessagesByUserAndLights($user, $lightsIds);
        }

        return $this->render(view: 'home/index.html.twig', parameters: [
            'lights' => $lights,
            'userMessages' => $userMessages,
        ]);
    }
}
