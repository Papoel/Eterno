<?php

declare(strict_types=1);

namespace App\Services\Encryptor;

final class DecryptService
{
    public function decrypt(string $encryptedData, string $hashedPassword): string
    {
        // Avant de déchiffrer le message, vérifie si la chaîne contient le séparateur '::'
        $decodedMessage = base64_decode($encryptedData);

        if (!$decodedMessage || !str_contains($decodedMessage, '::')) {
            return '';
        }

        [$encryptedMessage, $iv] = explode(separator: '::', string: base64_decode($encryptedData), limit: 2);

        $password = $hashedPassword;

        /** @var string $decrypted */
        $decrypted = openssl_decrypt(data: $encryptedMessage, cipher_algo: 'aes-256-cbc', passphrase: $password, iv: $iv);

        return $decrypted;
    }
}
