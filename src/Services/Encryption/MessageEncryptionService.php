<?php

declare(strict_types=1);

namespace App\Services\Encryption;

use App\Services\Encryptor\DecryptorInterface;
use App\Services\Encryptor\EncryptorInterface;

class MessageEncryptionService
{
    public function __construct(
        private readonly EncryptorInterface $encryptService,
        private readonly DecryptorInterface $decryptService,
    ) {
    }

    public function encrypt(?string $content, string $key): string
    {
        if (null === $content) {
            throw new \InvalidArgumentException('Content cannot be null');
        }

        return $this->encryptService->encrypt($content, $key);
    }

    public function decrypt(string $content, string $key): string
    {
        return $this->decryptService->decrypt($content, $key);
    }
}
