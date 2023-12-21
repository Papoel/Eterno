<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordManagerService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    // Verify if the current Password is the same as the one in the database

    public function isCurrentPasswordValid(User $user, string $currentPassword): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $currentPassword);
    }

    public function changePassword(User $user, string $newPassword): void
    {
        $this->userRepository->upgradePassword($user, $this->passwordHasher->hashPassword($user, $newPassword));
    }
}
