<?php

namespace App\Controller;

use App\Entity\Light;
use App\Entity\User;
use App\Form\LightType;
use App\Repository\LightRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            /* @phpstan-ignore-next-line */
            $light->setUserAccount($this->getUser());

            $entityManager->persist($light);
            $entityManager->flush();

            $this->addFlash(type: 'success', message: 'Votre Lumière '.$light->getFullname().' a bien été ajoutée');

            return $this->redirectToRoute(route: 'app_light_index', parameters: [], status: Response::HTTP_SEE_OTHER);
        }

        return $this->render(view: 'light/new.html.twig', parameters: [
            'light' => $light,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_light_show', methods: ['GET'])]
    public function show(Light $light): Response
    {
        return $this->render(view: 'light/show.html.twig', parameters: [
            'light' => $light,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_light_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Light $light, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(type: LightType::class, data: $light);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash(type: 'success', message: 'Votre Lumière '.$light->getFullname().' a bien été modifiée');

            return $this->redirectToRoute(route: 'app_light_index', parameters: [], status: Response::HTTP_SEE_OTHER);
        }

        return $this->render(view: 'light/edit.html.twig', parameters: [
            'light' => $light,
            'form' => $form,
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
