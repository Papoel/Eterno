<?php

namespace App\Controller\admin;

use App\Repository\InvitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/tableau-de-bord/gestion/invitations', name: 'admin.invitation.')]
#[IsGranted('ROLE_ADMIN')]
class InvitationController extends AbstractController
{
    #[Route('/liste', name: 'index', methods: ['GET'])]
    public function index(InvitationRepository $invitationRepository): Response
    {
        return $this->render(view: 'admin/invitations/index.html.twig', parameters: [
            'invitations' => $invitationRepository->findAllWithFriend(),
        ]);
    }
}
