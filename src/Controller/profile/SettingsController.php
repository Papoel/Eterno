<?php

namespace App\Controller\profile;

use App\Entity\User;
use App\Form\Account\ChangePasswordType;
use App\Form\Account\UserDataType;
use App\Services\PasswordManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile/parametres/{id}', name: 'profile_settings.')]
class SettingsController extends AbstractController
{
    public function __construct(
        private readonly PasswordManagerService $passwordManagerService,
    ) {
    }

    #[Route('/', name: 'index')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        Security $security,
    ): Response {
        /** @var User $user */
        $user = $security->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute(route: 'app_login');
        }

        $formUserData = $this->createForm(type: UserDataType::class, data: $this->getUser());
        $formUserPassword = $this->createForm(type: ChangePasswordType::class, data: $this->getUser());

        $formUserData->handleRequest($request);
        $formUserPassword->handleRequest($request);

        // Form User Data
        if ($formUserData->isSubmitted() && $formUserData->isValid()) {
            $em->flush();

            $this->addFlash(type: 'success', message: 'Vos informations ont bien été mises à jour.');

            return $this->redirectToRoute(route: 'profile_settings.index', parameters: [
                'id' => $user->getId(),
            ]);
        }

        if ($formUserData->isSubmitted() && !$formUserData->isValid()) {
            $errorMessages = [];
            foreach ($formUserData->getErrors(deep: true) as $error) {
                /* @phpstan-ignore-next-line */
                $errorMessages[] = $error->getMessage();
            }

            $this->addFlash(
                type: 'danger',
                message: 'Une erreur\'est produite lors de la mise à jour de vos informations. Veuillez réessayer.'
                .PHP_EOL.
                implode(separator: PHP_EOL, array: $errorMessages)
            );
        }

        // Form User Password
        if ($formUserPassword->isSubmitted() && $formUserPassword->isValid()) {
            /** @var string $currentPassword */
            $currentPassword = $formUserPassword->get('currentPassword')->getData();
            /** @var string $newPassword */
            $newPassword = $formUserPassword->get('newPassword')->getData();

            if ($this->passwordManagerService->isCurrentPasswordValid($user, $currentPassword)) {
                $this->passwordManagerService->changePassword($user, $newPassword);

                $this->addFlash(type: 'success', message: 'Votre mot de passe a bien été modifié.');

                return $this->redirectToRoute(route: 'profile_settings.index', parameters: [
                    'id' => $user->getId(),
                ]);
            }
        }

        if ($formUserPassword->isSubmitted() && !$formUserPassword->isValid()) {
            $errorMessages = [];
            foreach ($formUserPassword->getErrors(deep: true) as $error) {
                /* @phpstan-ignore-next-line */
                $errorMessages[] = $error->getMessage();
            }

            $this->addFlash(
                type: 'warning',
                message: 'Une erreur\'est produite lors de la mise à jour de votre mot de passe. Veuillez réessayer.'
                .PHP_EOL.
                implode(separator: PHP_EOL, array: $errorMessages)
            );
        }

        return $this->render(view: 'partials/settings/settings.html.twig', parameters: [
            'formUserData' => $formUserData->createView(),
            'formUserPassword' => $formUserPassword->createView(),
        ]);
    }
}
