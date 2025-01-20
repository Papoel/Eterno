<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\MessageRepository;
use App\Services\Encryption\MessageEncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:recrypt-messages',
    description: 'Re-chiffre les messages qui ne sont pas dans le bon format',
)]
class RecryptMessagesCommand extends Command
{
    // La déclaration des propriétés peut causer des soucis en fonction de la version PHP
    // Il est possible de déclarer directement les propriétés dans le constructeur si nécessaire
    public function __construct(
        private MessageRepository $messageRepository,
        private MessageEncryptionService $encryptionService,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $messages = $this->messageRepository->findAll();

        $io->progressStart(count($messages));
        $fixed = 0;

        foreach ($messages as $message) {
            $content = $message->getContent();

            // Vérifie que le contenu n'est pas null avant de le passer à isCorrectlyEncrypted
            if (null === $content || !$this->isCorrectlyEncrypted($content)) {
                $io->progressAdvance();
                continue;
            }

            // Récupère l'utilisateur qui a créé le message
            $user = $message->getUserAccount();
            if (!$user) {
                $io->progressAdvance();
                continue;
            }

            // Re-chiffre le contenu avec le mot de passe de l'utilisateur
            try {
                $encryptedContent = $this->encryptionService->encrypt(
                    $content,
                    $user->getPassword()
                );

                $message->setContent($encryptedContent);
                $this->entityManager->persist($message);
                ++$fixed;
            } catch (\Exception $e) {
                $io->error(sprintf('Erreur lors du chiffrement du message %s: %s', $message->getId(), $e->getMessage()));
            }

            $io->progressAdvance();
        }

        $this->entityManager->flush();
        $io->progressFinish();

        $io->success(sprintf('%d messages ont été re-chiffrés avec succès', $fixed));

        return Command::SUCCESS;
    }

    private function isCorrectlyEncrypted(string $content): bool
    {
        // Essaie de décoder en base64
        $decoded = base64_decode($content, true);
        if (false === $decoded) {
            return false;
        }

        // Vérifie la présence du séparateur
        return str_contains($decoded, '::');
    }
}
