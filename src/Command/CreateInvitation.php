<?php

namespace App\Command;

use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateInvitation extends Command
{
    public const CMD_NAME = 'app:create:invitation';

    private SymfonyStyle $io;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct(name: self::CMD_NAME);
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle(input: $input, output: $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $invitation = new Invitation();

        $email = $this->io->ask(question: 'Quel est l\'email de l\'ami ?');
        $listOfUsers = $this->em->getRepository(User::class)->findAll();
        $user = $this->io->choice(question: 'A quel utilisateur voulez-vous lier cette invitation ?', choices: $listOfUsers);

        /* @phpstan-ignore-next-line */
        $invitation->setEmail($email);
        /* @phpstan-ignore-next-line */
        $invitation->setFriend($user);

        // TODO: Envoyer Email à l'ami

        // Créer un service pour envoie de mail via command

        $this->em->persist($invitation);
        $this->em->flush();

        $this->io->success(message: 'Invitation créée avec succès');

        return Command::SUCCESS;
    }
}
