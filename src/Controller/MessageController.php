<?php

namespace App\Controller;

use App\DTO\MessageDTO;
use App\Entity\Message;
use App\Entity\User;
use App\Form\Message\MessageType;
use App\Services\Message\MessageManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/communication')]
#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    public function __construct(
        private readonly MessageManagerService $messageManager,
        protected CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    #[Route('/new/{receiver}', name: 'app_message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, string $receiver): Response
    {
        // https://localhost:8000/communication/new/01948542-4439-702f-8ce6-a089ac557787
        // Vérifier si $receiver existe
        $light = $this->messageManager->getLight(id: $receiver);

        // 1. Utilisateur, Light inexistante
        // 2. Aucun utilisateur connecté
        switch (true) {
            case !$light && $this->getUser():
                $this->addFlash(type: 'danger', message: 'Une erreur est survenue, veuillez réessayer.');

                return $this->redirectToRoute(route: 'home.user_connected');
            case !$this->getUser():
                throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $user = $this->getUser();

        if (!$user || !($user instanceof User)) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $messageDTO = new MessageDTO();
        $form = $this->createForm(type: MessageType::class, data: $messageDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->messageManager->createMessage(
                    dto: $messageDTO,
                    user: $user,
                    receiverId: $receiver
                );

                return $this->redirectToRoute(route: 'app_message_new', parameters: [
                    'receiver' => $receiver,
                ]);
            } catch (\InvalidArgumentException $e) {
                $form->get('content')->addError(new FormError($e->getMessage()));

                return new Response(
                    $this->renderView(view: 'message/index.html.twig', parameters: [
                        'light' => $this->messageManager->getLight(id: $receiver),
                        'messages' => $this->messageManager->getUserMessages(user: $user, receiverId: $receiver),
                        'form' => $form,
                    ]),
                    status: Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            return new Response(
                content: $this->renderView(view: 'message/index.html.twig', parameters: [
                    'light' => $this->messageManager->getLight(id: $receiver),
                    'messages' => $this->messageManager->getUserMessages(user: $user, receiverId: $receiver),
                    'form' => $form,
                ]),
                status: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $this->render(view: 'message/index.html.twig', parameters: [
            'light' => $this->messageManager->getLight(id: $receiver),
            'messages' => $this->messageManager->getUserMessages(user: $user, receiverId: $receiver),
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'app_message_delete', methods: ['POST'])]
    public function delete(Request $request, Message $message): Response
    {
        $token = $request->request->getString(key: '_token');
        $messageId = $message->getId();

        if (null === $messageId) {
            throw new \RuntimeException(message: 'Message ID cannot be null');
        }

        if (!$this->isCsrfTokenValid(id: 'delete'.$messageId->toRfc4122(), token: $token)) {
            throw $this->createAccessDeniedException(message: 'Token CSRF invalide');
        }

        $this->messageManager->deleteMessage(
            messageId: $messageId->toRfc4122(),
            token: $token
        );

        return $this->redirectToRoute(route: 'app_message_new', parameters: [
            'receiver' => $request->get(key: 'receiver'),
        ]);
    }
}
