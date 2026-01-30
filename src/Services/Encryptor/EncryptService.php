<?php

declare(strict_types=1);

namespace App\Services\Encryptor;

readonly class EncryptService implements EncryptorInterface
{
    private const CIPHER_METHOD = 'aes-256-cbc';
    private const SEPARATOR = '::';

    /**
     * Encrypts data using AES-256-CBC encryption.
     *
     * @param string $data       Data to encrypt
     * @param string $privateKey Key used for encryption
     *
     * @return string Base64 encoded encrypted data with IV
     *
     * @throws \RuntimeException If encryption fails
     */
    public function encrypt(string $data, string $privateKey): string
    {
        // Validate inputs
        if (empty($data)) {
            throw new \RuntimeException(message: 'Les données à chiffrer ne peuvent pas être vides.');
        }

        if (empty($privateKey)) {
            throw new \RuntimeException(message: 'La clé privée ne peut pas être vide.');
        }

        // Get cipher IV length (validated in constructor, so this will always return a valid int)
        $ivLength = openssl_cipher_iv_length(cipher_algo: self::CIPHER_METHOD);

        // Generate random IV
        $iv = openssl_random_pseudo_bytes($ivLength);
        if (!$iv) {
            throw new \RuntimeException(message: 'Echec de la génération du vecteur d\'initialisation.');
        }

        // Perform encryption
        $encrypted = openssl_encrypt(
            data: $data,
            cipher_algo: self::CIPHER_METHOD,
            passphrase: $privateKey,
            options: OPENSSL_RAW_DATA,
            iv: $iv
        );

        if (false === $encrypted) {
            throw new \RuntimeException(message: 'Le chiffrement a échoué : '.openssl_error_string());
        }

        // Combine encrypted data with IV and encode
        try {
            $combined = base64_encode(string: $encrypted).self::SEPARATOR.base64_encode(string: $iv);

            return base64_encode(string: $combined);
        } catch (\Exception $e) {
            throw new \RuntimeException(message: 'Échec de l\'encodage des données chiffrées : '.$e->getMessage());
        }
    }

    /**
     * Validates that the encryption method is available.
     *
     * @throws \RuntimeException if the encryption method is not available
     */
    public function __construct()
    {
        if (!in_array(needle: self::CIPHER_METHOD, haystack: openssl_get_cipher_methods(), strict: true)) {
            throw new \RuntimeException(sprintf('Encryption method %s is not available on this system', self::CIPHER_METHOD));
        }
    }

    /**
     * Get the cipher method used for encryption.
     */
    public function getCipherMethod(): string
    {
        return self::CIPHER_METHOD;
    }
}
