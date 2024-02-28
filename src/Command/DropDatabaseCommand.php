<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DropDatabaseCommand extends Command
{
    public const CMD_NAME = 'app:drop:database';
    private SymfonyStyle $io;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct(name: self::CMD_NAME);
    }

    protected function configure(): void
    {
        $this->setDescription(description: 'Efface toutes les données de la base de données');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle(input: $input, output: $output);
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title('Effacement de la base de données');

        $this->io->caution('Cette action est irréversible.');

        if ($this->io->confirm(question: 'Voulez-vous vraiment effacer toutes les données de la base de données ?')) {
            $this->entityManager->getConnection()->executeStatement(sql: 'SET FOREIGN_KEY_CHECKS=0');
            $this->entityManager->getConnection()->executeStatement(sql: 'TRUNCATE TABLE invitations');
            $this->entityManager->getConnection()->executeStatement(sql: 'TRUNCATE TABLE lights');
            $this->entityManager->getConnection()->executeStatement(sql: 'TRUNCATE TABLE messages');
            $this->entityManager->getConnection()->executeStatement(sql: 'TRUNCATE TABLE users');
            $this->entityManager->getConnection()->executeStatement(sql: 'SET FOREIGN_KEY_CHECKS=1');

            $this->io->success('Toutes les données de la base de données ont été effacées.');
        }

        return Command::SUCCESS;
    }
}
