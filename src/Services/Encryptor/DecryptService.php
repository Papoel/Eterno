<?php

declare(strict_types=1);

namespace App\Services\Encryptor;

readonly class DecryptService implements DecryptorInterface
{
    private const CIPHER_METHOD = 'aes-256-cbc';
    private const SEPARATOR = '::';

    /**
     * Decrypts data that was encrypted using EncryptService.
     *
     * @param string $encryptedData  Base64 encoded encrypted data with IV
     * @param string $hashedPassword Password used for decryption
     *
     * @return string Decrypted data
     *
     * @throws \RuntimeException If decryption fails
     */
    public function decrypt(string $encryptedData, string $hashedPassword): string
    {
        // Validate inputs
        if (empty($encryptedData)) {
            return '';
        }

        if (empty($hashedPassword)) {
            throw new \RuntimeException(message: 'Le mot de passe ne peut pas être vide.');
        }

        try {
            // First base64 decode to get the combined string
            $decodedCombined = base64_decode(string: $encryptedData, strict: true);

            // Si le décodage échoue, le message n'est peut-être pas chiffré
            if (false === $decodedCombined) {
                // Si la chaîne semble être du texte lisible, on la retourne telle quelle
                if (preg_match('/^[\p{L}\p{N}\s\p{P}]+$/u', $encryptedData)) {
                    return $encryptedData;
                }

                return '[Message illisible]';
            }

            // Check for separator
            if (!str_contains($decodedCombined, self::SEPARATOR)) {
                // Si la chaîne décodée semble être du texte lisible, on la retourne
                if (preg_match('/^[\p{L}\p{N}\s\p{P}]+$/u', $decodedCombined)) {
                    return $decodedCombined;
                }

                return '[Message illisible]';
            }

            // Split the decoded string into encrypted data and IV
            [$base64EncryptedData, $base64Iv] = explode(separator: self::SEPARATOR, string: $decodedCombined, limit: 2);

            // Decode both parts
            $encryptedData = base64_decode(string: $base64EncryptedData, strict: true);
            $iv = base64_decode(string: $base64Iv, strict: true);

            if (false === $encryptedData || false === $iv) {
                return '[Message illisible]';
            }

            // Perform decryption
            $decrypted = openssl_decrypt(
                data: $encryptedData,
                cipher_algo: self::CIPHER_METHOD,
                passphrase: $hashedPassword,
                options: OPENSSL_RAW_DATA,
                iv: $iv
            );

            if (false === $decrypted) {
                return '[Message illisible]';
            }

            return $decrypted;
        } catch (\Exception $e) {
            return '[Message illisible]';
        }
    }

    /**
     * Validates that the decryption method is available.
     *
     * @throws \RuntimeException if the decryption method is not available
     */
    public function __construct()
    {
        if (!in_array(needle: self::CIPHER_METHOD, haystack: openssl_get_cipher_methods(), strict: true)) {
            throw new \RuntimeException(sprintf('Decryption method %s is not available on this system', self::CIPHER_METHOD));
        }
    }

    /**
     * Get the cipher method used for decryption.
     */
    public function getCipherMethod(): string
    {
        return self::CIPHER_METHOD;
    }
}
