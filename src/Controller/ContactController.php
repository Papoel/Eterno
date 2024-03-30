<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Entity\User;
use App\Form\ContactType;
use App\Services\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, MailerService $mailerService): Response
    {
        $user = $this->getUser();
        $data = new ContactDTO();

        if ($user instanceof User) {
            // Utiliser getEmail() uniquement si $user est une instance de User et que son email n'est pas null
            $email = $user->getEmail();
            if (null !== $email) {
                $data->email = $email;
            }
            $name = $user->getFullName();
            if (null !== $name) {
                $data->name = $name;
            }
        }
        $form = $this->createForm(type: ContactType::class, data: $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if ($user instanceof User) {
                    /* @phpstan-ignore-next-line */
                    $data->email = $this->getUser()->getEmail();
                }
                $mailerService->contact($form);

                $this->addFlash(
                    type: 'info',
                    message: 'Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.'
                );
            } catch (TransportExceptionInterface $e) {
                $this->addFlash(
                    type: 'warning',
                    message: 'Une erreur est survenue lors de l\'envoi du message. Veuillez réessayer plus tard.'
                );
            }

            return $this->redirectToRoute(route: 'home.index');
        }

        return $this->render(view: 'contact/contact.html.twig', parameters: [
            'form' => $form,
        ]);
    }
}
