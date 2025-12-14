<?php

use App\Services\Encryptor\DecryptService;
use App\Services\Encryptor\EncryptService;

beforeEach(function (): void {
    $this->decryptService = new DecryptService();
    $this->encryptService = new EncryptService();
});

test(description: 'Le service de déchiffrement est instantiable', closure: function (): void {
    expect($this->decryptService)->toBeInstanceOf(class: DecryptService::class);
});

test(description: 'Le service de déchiffrement peut déchiffrer des données chiffrées', closure: function (): void {
    $originalData = 'Test message 123!';
    $password = 'secret_password';

    $encrypted = $this->encryptService->encrypt(data: $originalData, privateKey: $password);
    $decrypted = $this->decryptService->decrypt(encryptedData: $encrypted, hashedPassword: $password);

    expect($decrypted)->toBe($originalData);
});


test(description: 'Le mot de passe vide lève une exception', closure: function (): void {
    expect(fn() => $this->decryptService->decrypt(encryptedData: 'some_data', hashedPassword: ''))
        ->toThrow(exception: RuntimeException::class, exceptionMessage: 'Le mot de passe ne peut pas être vide.');
});

test(description: 'La méthode de chiffrement correspond au service de chiffrement', closure: function (): void {
    expect(value: $this->decryptService->getCipherMethod())
        ->toBe(expected: $this->encryptService->getCipherMethod());
});
