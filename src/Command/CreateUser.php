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
        $username = $this->io->ask(question: 'Quel est le pseudo de l\'utilisateur ?');
        $email = $this->io->ask(question: 'Quel est l\'email de l\'utilisateur ?');
        $password = $this->io->askHidden(question: 'Quel est le mot de passe de l\'utilisateur ?');
        $birthdate = $this->io->ask(question: 'Quel est la date de naissance de l\'utilisateur ?');
        $mobile = $this->io->ask(question: 'Quel est le numéro de téléphone de l\'utilisateur ?');

        $user = new User();
        /* @phpstan-ignore-next-line */
        $user->setFirstname($firstname);
        /* @phpstan-ignore-next-line */
        $user->setLastname($lastname);
        /* @phpstan-ignore-next-line */
        $user->setUsername($username);
        /* @phpstan-ignore-next-line */
        $user->setEmail($email);
        /* @phpstan-ignore-next-line */
        $user->setPassword($this->passwordHasher->hashPassword(user: $user, plainPassword: $password));

        /** @phpstan-ignore-next-line */
        $dateObj = \DateTime::createFromFormat('d/m/Y', $birthdate);
        if (false === $dateObj) {
            throw new \InvalidArgumentException('Date de naissance invalide');
        }

        /* @phpstan-ignore-next-line */
        $user->setBirthday($dateObj);
        /* @phpstan-ignore-next-line */
        $user->setMobile($mobile);

        $this->em->persist($user);
        $this->em->flush();

        $this->io->success('L\'utilisateur a bien été créé !');

        return Command::SUCCESS;
    }
}
