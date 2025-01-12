<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute(route: 'home.user_connected');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            view: 'security/login.html.twig',
            parameters: [
                'last_username' => $lastUsername,
                'error' => $error,
            ]
        );
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(RequestStack $requestStack): void
    {
        $requestStack->getSession()->remove(name: 'user');

        throw new \LogicException(message: 'This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/delete-account/{id}', name: 'app_delete_account')]
    public function deleteAccount(
        User $user,
        EntityManagerInterface $entityManager,
        RequestStack $requestStack,
    ): Response {
        $userFullname = $user->getFullname();

        if ($userFullname === $requestStack->getSession()->get(name: 'user')) {
            $requestStack->getSession()->remove(name: 'user');
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash(type: 'success', message: 'Votre compte a bien été supprimé.');
            $this->redirectToRoute(route: 'home.index');
        }

        return $this->redirectToRoute(route: 'home.index');
    }
}
