<?php

namespace App\Controller\admin;

use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/tableau-de-bord/gestion/messages', name: 'admin.message.')]
#[IsGranted('ROLE_ADMIN')]
class MessageController extends AbstractController
{
    #[Route('/liste', name: 'index', methods: ['GET'])]
    public function index(MessageRepository $messageRepository): Response
    {
        return $this->render(view: 'admin/messages/index.html.twig', parameters: [
            'messages' => $messageRepository->findAllWithRelations(),
        ]);
    }
}
