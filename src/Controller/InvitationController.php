<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Entity\User;
use App\Form\Account\RegistrationFormType;
use App\Security\MainAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class InvitationController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    #[Route('/invitation/{uuid}', name: 'invitation.verify')]
    public function verify(
        Request $request,
        Invitation $invitation,
        UserAuthenticatorInterface $userAuthenticator,
        MainAuthenticator $authenticator,
    ): Response {
        if ($invitation->isAccepted()) {
            $this->addFlash(type: 'info', message: 'Vous avez déjà accepté l\'invitation, veuillez vous connecter');

            return $this->redirectToRoute(route: 'app_login');
        }

        $user = new User();
        $user->setEmail($invitation->getEmail());

        $form = $this->createForm(type: RegistrationFormType::class, data: $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verify if the email is already used in the database
            $userInDb = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $form->get('email')->getData()]);
            if ($userInDb) {
                $this->addFlash(type: 'danger', message: 'Cet email est déjà utilisé');

                return $this->redirectToRoute(route: 'app_login');
            }

            /** @phpstan-ignore-next-line */
            $plainPassword = (string) $form->get('password')->getData();

            $user->setPassword(
                password: $this->userPasswordHasher->hashPassword(
                    user: $user,
                    plainPassword: $plainPassword
                )
            );

            $invitation->setAccepted(accepted: true);
            $invitation->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            /* @phpstan-ignore-next-line */
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render(view: 'security/register.html.twig', parameters: [
            'registrationForm' => $form->createView(),
        ]);
    }
}
