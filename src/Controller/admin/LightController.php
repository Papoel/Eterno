<?php

namespace App\Controller\admin;

use App\Repository\LightRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/tableau-de-bord/gestion/lumieres', name: 'admin.light.')]
#[IsGranted('ROLE_ADMIN')]
class LightController extends AbstractController
{
    #[Route('/liste', name: 'index', methods: ['GET'])]
    public function index(LightRepository $lightRepository): Response
    {
        return $this->render(view: 'admin/lights/index.html.twig', parameters: [
            'lights' => $lightRepository->findAllWithRelations(),
        ]);
    }
}
