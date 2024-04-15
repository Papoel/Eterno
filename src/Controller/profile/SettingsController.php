<?php

namespace App\Controller\profile;

use App\Entity\Invitation;
use App\Entity\User;
use App\Form\Account\AvatarType;
use App\Form\Account\ChangePasswordType;
use App\Form\Account\InvitationType;
use App\Form\Account\UserDataType;
use App\Repository\InvitationRepository;
use App\Services\MailerService;
use App\Services\PasswordManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profile/parametres/{id}', name: 'profile_settings.')]
class SettingsController extends AbstractController
{
    public function __construct(
        private readonly PasswordManagerService $passwordManagerService,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    #[Route('/', name: 'index')]
    public function index(
        Request $request,
        Security $security,
        InvitationRepository $invitationRepository,
        MailerService $mailerService
    ): Response {
        /** @var User $user */
        $user = $security->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute(route: 'app_login');
        }

        $formUserData = $this->createForm(type: UserDataType::class, data: $this->getUser());
        $formUserPassword = $this->createForm(type: ChangePasswordType::class, data: $this->getUser());
        $formAvatar = $this->createForm(type: AvatarType::class, data: $this->getUser());
        $formInvitation = $this->createForm(type: InvitationType::class);

        $formUserData->handleRequest($request);
        $formUserPassword->handleRequest($request);
        $formAvatar->handleRequest($request);
        $formInvitation->handleRequest($request);

        // Form User Data
        if ($formUserData->isSubmitted() && $formUserData->isValid()) {
            $this->em->flush();

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

        // Form Avatar
        if ($formAvatar->isSubmitted() && $formAvatar->isValid()) {
            // Récupérer le fichier
            $avatarFile = $formAvatar->get('avatarFile')->getData();

            if (null !== $avatarFile) {
                // Récupérer le type mime du fichier
                /** @phpstan-ignore-next-line */
                $avatarMimeType = $avatarFile->getMimeType();
                // Lister les types de fichiers autorisés
                $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                // Vérifier si le type mime du fichier est autorisé
                if (!in_array(needle: $avatarMimeType, haystack: $allowedMimeTypes, strict: true)) {
                    $this->addFlash(type: 'danger', message: 'Le type de fichier n\'est pas autorisé, un avatar par défaut a été appliqué.');
                    // Définir AvatarFile à null dans la base de données
                    $user->setAvatar(avatar: null);
                    $this->em->flush();

                    // REDIRECTION
                    return $this->redirectToRoute(route: 'profile_settings.index');
                }

                // Enregistrer l'avatar.
                /* @var UploadedFile $avatarFile */
                /* @phpstan-ignore-next-line */
                $user->setAvatarFile(avatarFile: $avatarFile);
                $this->em->flush();
                $this->addFlash(type: 'success', message: 'Votre avatar a bien été modifié.');
            } else {
                $this->addFlash(type: 'danger', message: 'Veuillez sélectionner un fichier avant de valider.');
            }
        }

        // Form Invitation
        if ($formInvitation->isSubmitted() && $formInvitation->isValid()) {
            try {
                // Vérifier si l'email existe d'invitation existe déjà dans la base de données
                $invitation = $invitationRepository->findOneBy(['email' => $formInvitation->get('email')->getData()]);

                if (null !== $invitation) {
                    $status = $invitation->isAccepted() ? 'acceptée' : 'en attente';

                    // Vérifier si l'invitation a déjà été acceptée
                    if ($invitation->isAccepted()) {
                        $this->addFlash(type: 'success', message: 'Cet utilisateur a déjà reçu une invitation, son invitation est '.$status.'.');
                    }

                    // Vérifier si l'invitation a déjà été envoyée
                    if (!$invitation->isAccepted()) {
                        $this->addFlash(type: 'warning', message: 'Cet utilisateur a déjà reçu une invitation, son invitation est '.$status.'.');
                    }

                    return $this->redirectToRoute(route: 'profile_settings.index', parameters: [
                        'id' => $user->getId(),
                    ]);
                }

                if ($formInvitation->getData() instanceof Invitation) {
                    $formInvitation->getData()->setFriend(friend: $user);
                    $mailerService->sendInvitationEmail(form: $formInvitation);
                    $this->addFlash(type: 'success', message: 'Votre invitation a bien été envoyée.');
                }

                /* @phpstan-ignore-next-line */
                $this->em->persist($formInvitation->getData());
                $this->em->flush();
            } catch (TransportExceptionInterface $e) {
                $this->addFlash(type: 'warning', message: 'Une erreur est survenue lors de l\'envoi de l\'invitation. Veuillez réessayer plus tard.');
            }

            return $this->redirectToRoute(route: 'profile_settings.index', parameters: [
                'id' => $user->getId(),
            ]);
        }

        // Get Invitation send by the user
        $invitations = $invitationRepository->findBy(['friend' => $user]);

        return $this->render(view: 'settings/settings.html.twig', parameters: [
            'formUserData' => $formUserData->createView(),
            'formUserPassword' => $formUserPassword->createView(),
            'formAvatar' => $formAvatar->createView(),
            'formInvitation' => $formInvitation->createView(),
            'invitations' => $invitations,
        ]);
    }

    #[Route('/supprimer-avatar', name: 'delete_avatar')]
    public function deleteAvatar(): Response
    {
        /* @var User|null $user */
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (null === $user) {
            $this->addFlash(type: 'danger', message: 'Vous devez être connecté pour effectuer cette action.');

            return $this->redirectToRoute(route: 'app_login');
        }

        /** @phpstan-ignore-next-line */
        $directory = $this->getParameter(name: 'kernel.project_dir').'/assets/uploads/avatar';

        // Utiliser Finder pour lister les fichiers dans le dossier
        $finder = new Finder();
        $finder->in($directory);

        $files = [];
        foreach ($finder->files() as $file) {
            $files[] = $file->getRelativePathname();
        }

        // Rechercher les fichiers manquants
        $missingFiles = [];
        foreach ($files as $file) {
            /** @var User $user */
            $avatarFileName = $user->getAvatar();
            if (!in_array(needle: $avatarFileName, haystack: $files, strict: true)) {
                // Mettre en base de données l'avatar à null
                $user->setAvatar(avatar: null);
                $this->em->flush();
                $this->addFlash(type: 'danger', message: 'Votre avatar a été supprimé car il n\'existe plus dans le dossier.');
            }
        }

        if ($user instanceof User) {
            // Récupérer le nom de l'avatar puis le supprimer du dossier
            $avatar = $user->getAvatar();
            $uploadBasePath = $this->getParameter(name: 'upload_base_path');
            $avatarPath = '';

            if (null !== $uploadBasePath && null !== $avatar) {
                /** @phpstan-ignore-next-line */
                $avatarPath = $directory.'/'.$avatar;
                // Vérifier si le fichier existe
                if (file_exists(filename: $avatarPath)) {
                    // Supprimer le fichier
                    unlink(filename: $avatarPath);
                    $user->setAvatar(avatar: null);
                    $this->em->flush();
                    $this->addFlash(type: 'success', message: 'Votre avatar a bien été supprimé.');
                }
            }
        } else {
            $this->addFlash(type: 'danger', message: 'Vous devez être connecté pour effectuer cette action.');

            return $this->redirectToRoute(route: 'app_login');
        }

        return $this->redirectToRoute(route: 'profile_settings.index', parameters: ['id' => $user->getId()]);
    }
}
