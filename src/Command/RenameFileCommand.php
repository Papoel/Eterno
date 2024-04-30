<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:rename_file',
    description: 'Vérifie et renomme un fichier si nécessaire.'
)]
class RenameFileCommand extends Command
{
    private SymfonyStyle $io;

    protected function configure(): void
    {
        $this->setDescription(description: 'Vérifie et renomme un fichier si nécessaire.');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle(input: $input, output: $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Chemin des répertoires où se trouvent les fichiers
        $directories = [
            'public/assets/uploads/avatar/',
            'public/assets/uploads/photos/default/',
        ];

        // Noms des fichiers attendus dans chaque répertoire
        $expectedFileNames = [
            'placeholder.jpg',
            'default.jpg',
        ];

        // Parcourir les répertoires
        foreach ($directories as $key => $directory) {
            // Vérifier si le répertoire existe
            if (!file_exists($directory)) {
                $this->io->error(sprintf('Le répertoire %s n\'existe pas.', $directory));
                continue;
            }

            $expectedFileName = $expectedFileNames[$key];
            $filePath = $directory.$expectedFileName;

            // Vérifier si le fichier existe
            if (file_exists($filePath)) {
                $this->io->success(sprintf('Le fichier %s est déjà nommé comme prévu.', $expectedFileName));
            } else {
                // Si le fichier n'existe pas, renommer le fichier attendu s'il existe

                /*
                 * Test après discussion avec Aurélien le @ permet de supprimer le rapport d'erreur
                 * TODO: Effacer le @ pour voir les erreurs et les traiter
                 */
                $files = @scandir($directory);

                if (false !== $files) {
                    foreach ($files as $file) {
                        if ('.' !== $file && '..' !== $file && $file !== $expectedFileName) {
                            rename($directory.$file, $filePath);
                            $this->io->success(sprintf('Le fichier %s a été renommé avec succès.', $expectedFileName));
                            break;
                        }
                    }
                } else {
                    $this->io->error(sprintf('Impossible de lire le contenu du répertoire %s.', $directory));
                }
            }
        }

        return Command::SUCCESS;
    }
}
