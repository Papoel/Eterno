<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUser extends Command
{
    public const CMD_NAME = 'app:create:user';

    private SymfonyStyle $io;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct(name: self::CMD_NAME);
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle(input: $input, output: $output);
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $firstname = $this->io->ask(question: 'Quel est le prénom de l\'utilisateur ?');
        $lastname = $this->io->ask(question: 'Quel est le nom de l\'utilisateur ?');
        $email = $this->io->ask(question: 'Quel est l\'email de l\'utilisateur ?');
        $password = $this->io->askHidden(question: 'Quel est le mot de passe de l\'utilisateur ?');
        // Boolean isAdmin
        $isAdmin = $this->io->confirm(question: 'Est-ce un administrateur ?', default: false);

        $user = new User();
        /* @phpstan-ignore-next-line */
        $user->setFirstname($firstname);
        /* @phpstan-ignore-next-line */
        $user->setLastname($lastname);
        /* @phpstan-ignore-next-line */
        $user->setEmail($email);
        /* @phpstan-ignore-next-line */
        $user->setPassword($this->passwordHasher->hashPassword(user: $user, plainPassword: $password));
        /* @phpstan-ignore-next-line */
        $user->setRoles(roles: $isAdmin ? ['ROLE_ADMIN'] : ['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        $this->io->success('L\'utilisateur a bien été créé !');

        return Command::SUCCESS;
    }
}
