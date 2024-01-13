<?php

namespace App\Controller;

use App\Entity\Light;
use App\Entity\Message;
use App\Entity\User;
use App\Form\Message\MessageType;
use App\Repository\LightRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

#[Route('/communication')]
#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    #[Route('/new/{receiver}', name: 'app_message_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        MessageRepository $messageRepository,
        LightRepository $lightRepository,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User|null $user */
        $user = $this->getUser();
        $lights = [];
        $userMessages = [];

        // 1. Je récupère tous les objets Light de l'utilisateur connecté
        if (null !== $user) {
            $lights = $user->getLights();
            $lightsIds = [];
            foreach ($lights as $light) {
                $lightId = $light->getId();

                if ($lightId instanceof Uuid) {
                    $lightsIds[] = $lightId->toRfc4122();
                }
            }
        }

        // 2. Je récupère tous les messages envoyés par l'utilisateur connecté
        if ($user instanceof User) {
            /** @phpstan-ignore-next-line */
            $userMessages = $messageRepository->findMessagesByUserAndLights($user, $lightsIds);
        }

        $message = new Message();
        $form = $this->createForm(type: MessageType::class, data: $message);
        $emptyForm = clone $form;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user instanceof User) {
                $message->setUserAccount($user);
            }

            $light = $lightRepository->findOneBy(['id' => $request->get(key: 'receiver')]);
            if ($light instanceof Light) {
                $message->setLight($light);
            }

            $entityManager->persist($message);
            $entityManager->flush();

            $form = $emptyForm;

            // Redirection vers la même page pour éviter de renvoyer le formulaire
            return $this->redirectToRoute(route: 'app_message_new', parameters: [
                'receiver' => $request->get(key: 'receiver'),
            ]);
        }

        $receiver = $request->get(key: 'receiver');

        return $this->render(view: 'message/index.html.twig', parameters: [
            'light' => $lightRepository->findOneBy(['id' => $receiver]),
            'messages' => $userMessages,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_message_delete', methods: ['POST'])]
    public function delete(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        /* @phpstan-ignore-next-line */
        if ($this->isCsrfTokenValid(id: 'delete'.$message->getId(), token: $request->request->get(key: '_token'))) {
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute(route: 'app_home', parameters: [], status: Response::HTTP_SEE_OTHER);
    }
}
