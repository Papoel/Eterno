<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use App\Services\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, MailerService $mailerService): Response
    {
        $data = new ContactDTO();

        $data->name = 'Luc';
        $data->email = 'test@eterno.fr';
        $data->subject = 'Sujet de test';
        $data->message = 'Message de test';

        $form = $this->createForm(type: ContactType::class, data: $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $mailerService->contact($form);
            } catch (TransportExceptionInterface $e) {
                $this->addFlash(type: 'success', message: 'Votre message a bien été envoyé');
            }
        }

        return $this->render(view: 'contact/contact.html.twig', parameters: [
            'form' => $form,
        ]);
    }
}
