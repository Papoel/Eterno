<?php

declare(strict_types=1);

namespace App\Services;

readonly class EncryptService
{
    public function encrypt(string $data, string $privateKey): string
    {
        $method = 'aes-256-cbc';

        $ivLength = openssl_cipher_iv_length($method);
        if (false === $ivLength) {
            throw new \RuntimeException(message: 'Impossible de récupérer la longueur du vecteur d\'initialisation.');
        }

        $iv = openssl_random_pseudo_bytes($ivLength);
        if (!$iv) {
            throw new \RuntimeException(message: 'Erreur lors de la génération du vecteur d\'initialisation.');
        }

        $encrypted = openssl_encrypt(data: $data, cipher_algo: $method, passphrase: $privateKey, iv: $iv);

        return base64_encode(string: $encrypted.'::'.$iv);
    }
}
