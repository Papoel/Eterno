<?php

namespace App\Controller\admin;

use App\Repository\InvitationRepository;
use App\Repository\LightRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/tableau-de-bord', name: 'admin.')]
class AdminController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private MessageRepository $messageRepository,
        private LightRepository $lightRepository,
        private InvitationRepository $invitationRepository,
    ) {
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(attribute: 'ROLE_ADMIN');

        $totalUsers = $this->userRepository->countUsers();
        $invitationsAccepted = $this->invitationRepository->countInvitationsAccepted();
        $invitationsNotAccepted = $this->invitationRepository->countInvitationsNotAccepted();
        $totalMessages = $this->messageRepository->countMessages();
        $totalLights = $this->lightRepository->countLights();

        return $this->render(view: 'admin/index.html.twig', parameters: [
            'totalUsers' => $totalUsers,
            'invitationsAccepted' => $invitationsAccepted,
            'invitationsNotAccepted' => $invitationsNotAccepted,
            'totalMessages' => $totalMessages,
            'totalLights' => $totalLights,
        ]);
    }
}
