<?php

namespace App\Controller;

use App\Entity\Light;
use App\Entity\User;
use App\Form\Light\LightType;
use App\Repository\LightRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/lumiere')]
#[IsGranted('ROLE_USER')]
class LightController extends AbstractController
{
    #[Route('/', name: 'app_light_index', methods: ['GET'])]
    public function index(LightRepository $lightRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $lights = $lightRepository->findBy(['userAccount' => $user]);

        return $this->render(view: 'light/index.html.twig', parameters: [
            'lights' => $lights,
        ]);
    }

    #[Route('/ajouter', name: 'app_light_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $light = new Light();
        $form = $this->createForm(type: LightType::class, data: $light);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération de l'image
            $photo = $form->get('photo')->getData();

            if ($photo instanceof File) {
                $avatarMimeType = $photo->getMimeType();
                $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                if (!in_array($avatarMimeType, $allowedMimeTypes, true)) {
                    $this->addFlash(type: 'danger', message: 'Le type de fichier n\'est pas autorisé, un avatar par défaut a été appliqué.');
                    // Définir Photo à null dans l'entité
                    $light->setPhoto(photo: null);
                } else {
                    // Enregistrer la photo de profil.
                    $light->setPhotoFile(photoFile: $photo);
                }
            }

            // Vérifier si l'utilisateur connecté est bien une instance de User
            $user = $this->getUser();
            if ($user instanceof User) {
                // Ajouter l'utilisateur connecté
                $light->setUserAccount($user);

                // Sauvegarder
                $entityManager->persist($light);
                $entityManager->flush();
                $this->addFlash(type: 'success', message: 'Votre Lumière '.$light->getFullname().' a bien été ajoutée.');

                return $this->redirectToRoute(route: 'app_light_index');
            }

            // Rediriger vers la page de connexion
            return $this->redirectToRoute(route: 'app_login');
        }

        return $this->render(view: 'light/new.html.twig', parameters: [
            'light' => $light,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_light_show', methods: ['GET'])]
    public function show(Light $light): Response
    {
        // Ne voir que les Lumière de l'utilisateur connecté
        if ($light->getUserAccount() !== $this->getUser()) {
            throw $this->createAccessDeniedException(message: 'Vous n\'avez pas accès à cette page.');
        }

        return $this->render(view: 'light/show.html.twig', parameters: [
            'light' => $light,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_light_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Light $light, EntityManagerInterface $entityManager): Response
    {
        // Ne voir que les Lumières de l'utilisateur connecté
        if ($light->getUserAccount() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette page.');
        }

        $form = $this->createForm(type: LightType::class, data: $light);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();

            if (null === $photo) {
                $photoPath = $light->getPhoto();
                $parameterName = 'kernel.project_dir';

                /* @phpstan-ignore-next-line */
                $photoAbsolutePath = implode(
                    separator: DIRECTORY_SEPARATOR,
                    array: [
                        $this->getParameter(name: $parameterName),
                        'assets',
                        'uploads',
                        'photos',
                        $photoPath,
                    ]
                );

                /* @phpstan-ignore-next-line */
                $defaultPhotoAbsolutePath = implode(
                    separator: DIRECTORY_SEPARATOR,
                    array: [
                        $this->getParameter(name: $parameterName),
                        'assets',
                        'uploads',
                        'photos',
                        'default',
                        $photoPath,
                    ]
                );

                if ('default.jpg' === $photoPath) {
                    $light->setPhotoFile(new File($defaultPhotoAbsolutePath));
                }

                $filesystem = new Filesystem();

                if ($filesystem->exists($photoAbsolutePath)) {
                    $light->setPhotoFile(new File($photoAbsolutePath));
                }
            } else {
                // Nouvelle photo téléchargée, traitement normal
                if ($photo instanceof File) {
                    $avatarMimeType = $photo->getMimeType();
                    $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                    if (!in_array(needle: $avatarMimeType, haystack: $allowedMimeTypes, strict: true)) {
                        $this->addFlash(type: 'danger', message: 'Le type de fichier n\'est pas autorisé.');

                        return $this->redirectToRoute(route: 'app_light_edit', parameters: ['id' => $light->getId()]);
                    }

                    $light->setPhotoFile($photo);
                }
            }

            // Ajouter l'utilisateur connecté
            $user = $this->getUser();
            if ($user instanceof User) {
                $light->setUserAccount($user);

                // Sauvegarder
                $entityManager->persist($light);
                $entityManager->flush();
                $this->addFlash(type: 'success', message: 'Votre Lumière '.$light->getFullname().' a bien été modifiée.');

                return $this->redirectToRoute(route: 'app_light_index');
            }

            // Redirection vers la page de connexion
            return $this->redirectToRoute(route: 'app_login');
        }

        return $this->render(view: 'light/edit.html.twig', parameters: [
            'light' => $light,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_light_delete', methods: ['POST'])]
    public function delete(Request $request, Light $light, EntityManagerInterface $entityManager): Response
    {
        /* @phpstan-ignore-next-line */
        if ($this->isCsrfTokenValid(id: 'delete'.$light->getId(), token: $request->request->get(key: '_token'))) {
            $entityManager->remove($light);
            $entityManager->flush();

            $this->addFlash(type: 'danger', message: 'Votre Lumière '.$light->getFullname().' a bien été supprimée');
        }

        return $this->redirectToRoute(route: 'app_light_index', parameters: [], status: Response::HTTP_SEE_OTHER);
    }
}
