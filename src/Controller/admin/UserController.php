<?php

declare(strict_types=1);

namespace App\Controller\admin;

use App\Entity\User;
use App\Form\Admin\CreateUserType;
use App\Repository\InvitationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/tableau-de-bord/gestion/utilisateurs', name: 'admin.user.')]
class UserController extends AbstractController
{
    #[Route('/liste', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository, InvitationRepository $invitationRepository): Response
    {
        // Récupérer les statistiques des invitations
        $userInvitationStats = $invitationRepository->getUserInvitationStats();

        // Construire le tableau des invitations acceptées avec des clés sous forme de chaînes de caractères
        $invitationAccepted = [];
        foreach ($userInvitationStats as $stats) {
            $invitationAccepted[(string) $stats['userId']] = [
                'invitationsSent' => $stats['invitationsSent'],
                'invitationsAccepted' => (int) $stats['invitationsAccepted'],
                'pending' => $stats['invitationsSent'] - (int) $stats['invitationsAccepted'],
                'percent' => (int) (($stats['invitationsAccepted'] / $stats['invitationsSent']) * 100),
                'userId' => $stats['userId'],
            ];
        }

        return $this->render(view: 'admin/users/index.html.twig', parameters: [
            'users' => $userRepository->findAll(),
            'invitationAccepted' => $invitationAccepted,
            'userInvitationStats' => $userInvitationStats,
        ]);
    }

    #[Route('/creation', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(type: CreateUserType::class, data: $user, options: [
            'action' => $this->generateUrl(route: 'admin.user.create'),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(type: 'success', message: 'Vous avez ajouté '.$user->getFullName().' avec succès');

            return $this->redirectToRoute(route: 'admin.user.index');
        }

        return $this->render(view: 'admin/users/create.html.twig', parameters: [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['GET', 'POST'])]
    public function delete(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        InvitationRepository $invitationRepository,
        Request $request,
    ): Response {
        $user = $userRepository->find($request->attributes->get(key: 'id'));
        $invitations = $invitationRepository->findBy(['friend' => $user]);
        /** @var User $user */
        $messages = $user->getMessages();

        foreach ($invitations as $invitation) {
            $entityManager->remove($invitation);
        }

        foreach ($messages as $message) {
            $entityManager->remove($message);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash(type: 'success', message: 'Vous avez supprimé '.$user->getFullName().' avec succès');

        return $this->redirectToRoute(route: 'admin.user.index');
    }
}
