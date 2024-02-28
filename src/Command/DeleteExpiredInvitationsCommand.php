<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\InvitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteExpiredInvitationsCommand extends Command
{
    public const CMD_NAME = 'app:drop:invitations';
    private SymfonyStyle $io;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly InvitationRepository $invitationRepository,
    ) {
        parent::__construct(name: self::CMD_NAME);
    }

    protected function configure(): void
    {
        $this
            ->setDescription(description: "Supprime les invitations expirées qui n'ont pas été acceptées dans un délai d'un mois.")
            ->setHelp(help: "Cette commande supprime les invitations qui n'ont pas été acceptées dans un délai d'un mois à compter de la date de création.");
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle(input: $input, output: $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new \DateTimeImmutable();
        $oneMonthAgo = $now->modify(modifier: '-1 month');

        $listOfExpiredInvitations = $this->invitationRepository->findExpiredInvitations($oneMonthAgo);

        foreach ($listOfExpiredInvitations as $invitation) {
            $this->entityManager->remove($invitation);
        }

        $this->entityManager->flush();

        $total = count($listOfExpiredInvitations);
        switch ($total) {
            case 0:
                $this->io->success(message: 'Aucune invitation expirée à supprimer.');
                break;
            case 1:
                $this->io->success(message: 'Une invitation expirée a été supprimée avec succès.');
                break;
            default:
                $this->io->success(message: $total.' invitations expirées ont été supprimées avec succès.');
        }

        return Command::SUCCESS;
    }
}
