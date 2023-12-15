<?php

namespace App\Controller\profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    #[Route('/parametres', name: 'profile_settings.index')]
    public function index(): Response
    {
        return $this->render(view: 'partials/settings/settings.html.twig');
    }
}
