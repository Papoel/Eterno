<?php

declare(strict_types=1);

namespace App\Services\Encryptor;

interface EncryptorInterface
{
    public function encrypt(string $data, string $privateKey): string;
}
