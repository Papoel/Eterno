<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Entity\User;
use App\Form\Account\RegistrationFormType;
use App\Repository\InvitationRepository;
use App\Security\MainAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class InvitationController extends AbstractController
{
    public function __construct(
        private Readonly EntityManagerInterface $entityManager,
        private Readonly UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    #[Route('/invitation', name: 'app_check')]
    public function check(InvitationRepository $repo): Response
    {
        $invitation = $repo->findall();
        dd($invitation);
    }

    #[Route('/invitation/{uuid}', name: 'app_invitation')]
    public function index(
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

            $password = $form->get('password')->getData();
            // encode the plain password
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $invitation->setAccepted(accepted: true);
            $invitation->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($user);
            $this->entityManager->flush();

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
