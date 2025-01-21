<?php

declare(strict_types=1);

namespace App\Services\Message;

use App\DTO\MessageDTO;
use App\Entity\Light;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\LightRepository;
use App\Repository\MessageRepository;
use App\Services\Encryption\MessageEncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

readonly class MessageManagerService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageRepository $messageRepository,
        private LightRepository $lightRepository,
        private MessageEncryptionService $encryptionService,
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function createMessage(MessageDTO $dto, User $user, string $receiverId): void
    {
        $content = $dto->getContent();
        if (empty($content)) {
            throw new \InvalidArgumentException(message: 'Le contenu du message ne peut pas être vide');
        }

        $light = $this->lightRepository->find($receiverId);
        if (!$light) {
            throw new \InvalidArgumentException(message: 'Lumière non trouvée');
        }

        $message = new Message();
        $message->setUserAccount($user);
        $message->setLight($light);
        $message->setContent($this->encryptionService->encrypt(
            $content,
            $user->getPassword()
        ));

        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    /**
     * @return array<int, array{id: string, content: string, createdAt: \DateTimeInterface, light_id: string}>
     */
    public function getUserMessages(User $user, string $receiverId): array
    {
        $light = $this->lightRepository->find($receiverId);
        if (!$light) {
            return [];
        }

        /** @var array<int, array{id: string, content: string, createdAt: \DateTimeInterface, light_id: string}> $messages */
        $messages = $this->messageRepository->findMessagesByUserAndLight($user, $receiverId);

        if (empty($messages)) {
            return [];
        }

        return array_map(
            fn (array $messageData) => [
                'id' => $messageData['id'],
                'content' => $this->encryptionService->decrypt(
                    $messageData['content'],
                    $user->getPassword()
                ),
                'createdAt' => $messageData['createdAt'],
                'light_id' => $messageData['light_id'],
            ],
            $messages
        );
    }

    /**
     * Decrypts a message content using the user's password.
     *
     * @todo This method will be used for future feature ...
     *
     * @phpstan-ignore-next-line
     */
    private function decryptMessage(Message $message, User $user): Message
    {
        $content = $message->getContent();
        if (empty($content)) {
            $decryptedMessage = clone $message;
            $decryptedMessage->setContent(content: '');

            return $decryptedMessage;
        }

        $decryptedContent = $this->encryptionService->decrypt(
            $content,
            $user->getPassword()
        );

        $decryptedMessage = clone $message;
        $decryptedMessage->setContent(content: $decryptedContent);

        return $decryptedMessage;
    }

    public function deleteMessage(string $messageId, string $token): void
    {
        $message = $this->messageRepository->find($messageId);
        if (!$message) {
            throw new \RuntimeException(message: 'Message non trouvé');
        }

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken(id: 'delete'.$messageId, value: $token))) {
            throw new \RuntimeException(message: 'CSRF token invalide');
        }

        $this->entityManager->remove($message);
        $this->entityManager->flush();
    }

    public function getLight(string $id): ?Light
    {
        return $this->lightRepository->find($id);
    }
}
