<?php

use App\Services\Encryption\MessageEncryptionService;
use App\Services\Encryptor\DecryptorInterface;
use App\Services\Encryptor\EncryptorInterface;

beforeEach(function () {
    $this->encryptService = Mockery::mock(EncryptorInterface::class);
    $this->decryptService = Mockery::mock(DecryptorInterface::class);
    $this->service = new MessageEncryptionService(
        $this->encryptService,
        $this->decryptService
    );
});

test(description: 'Une exception est levée si le contenu est null', closure: function () {
    $this->service->encrypt(content: null, key: 'key');
})->throws(exception: \InvalidArgumentException::class);

test(description: 'Encrypt delegue pour crypter le service', closure: function () {
    $this->encryptService
        ->expects(something: 'encrypt')
        ->with('content', 'key')
        ->andReturns('encrypted');

    $result = $this->service->encrypt(content: 'content', key: 'key');
    expect($result)
        ->toBe(expected: 'encrypted');
});

test(description: 'Decrypt delegue pour decrypter le service', closure: function () {
    $this->decryptService
        ->expects(something: 'decrypt')
        ->with('encrypted', 'key')
        ->andReturns('decrypted');

    $result = $this->service->decrypt(content: 'encrypted', key: 'key');
    expect(value: $result)
        ->toBe(expected: 'decrypted');
});

test('le service de chiffrement lève une exception si le contenu est null', function () {
    $encryptor = mock(EncryptorInterface::class);
    $decryptor = mock(DecryptorInterface::class);

    $service = new MessageEncryptionService($encryptor, $decryptor);

    expect(fn() => $service->encrypt(null, 'key'))
        ->toThrow(InvalidArgumentException::class, 'Content cannot be null');
});

test('le service de chiffrement chiffre correctement un message non vide', function () {
    $content = 'Message test';
    $key = 'test_key';
    $encrypted = 'encrypted_content';

    $encryptor = mock(EncryptorInterface::class);
    $decryptor = mock(DecryptorInterface::class);

    $encryptor->shouldReceive('encrypt')
        ->with($content, $key)
        ->once()
        ->andReturn($encrypted);

    $service = new MessageEncryptionService($encryptor, $decryptor);

    $result = $service->encrypt($content, $key);
    expect($result)->toBe($encrypted);
});

test('le service de déchiffrement déchiffre correctement un message', function () {
    $encrypted = 'encrypted_content';
    $key = 'test_key';
    $decrypted = 'Message test';

    $encryptor = mock(EncryptorInterface::class);
    $decryptor = mock(DecryptorInterface::class);

    $decryptor->shouldReceive('decrypt')
        ->with($encrypted, $key)
        ->once()
        ->andReturn($decrypted);

    $service = new MessageEncryptionService($encryptor, $decryptor);

    $result = $service->decrypt($encrypted, $key);
    expect($result)->toBe($decrypted);
});
