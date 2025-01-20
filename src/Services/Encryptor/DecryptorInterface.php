<?php

declare(strict_types=1);

namespace App\Services\Encryptor;

interface DecryptorInterface
{
    public function decrypt(string $encryptedData, string $hashedPassword): string;
}
