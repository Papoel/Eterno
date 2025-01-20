<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Services\Encryptor\EncryptService;
use RuntimeException;

beforeEach(function () {
    $this->service = new EncryptService();
});

test(description: 'EncryptService est instantiable', closure: function () {
    expect($this->service)->toBeInstanceOf(class: EncryptService::class);
});

test(description: 'Encrypt renvoie une chaîne non vide', closure: function () {
    $result = $this->service->encrypt(data: 'test data', privateKey: 'secret key');
    expect($result)->toBeString()->not->toBeEmpty();
});

test(description: 'Le chiffrement avec la même entrée produit une sortie différente en raison d\'une IV aléatoire.', closure: function () {
    $result1 = $this->service->encrypt(data: 'test data', privateKey: 'secret key');
    $result2 = $this->service->encrypt(data: 'test data', privateKey: 'secret key');
    expect(value: $result1)->not->toBe(expected: $result2);
});

test(description: 'Les données à chiffrer ne peuvent pas être vides.', closure: function () {
    expect(fn() => $this->service->encrypt(data: '', privateKey: 'secret key'))
        ->toThrow(exception: RuntimeException::class, exceptionMessage: 'Les données à chiffrer ne peuvent pas être vides.');
});

test(description: 'La clé privée ne peut pas être vide.', closure: function () {
    expect(fn() => $this->service->encrypt(data: 'test data', privateKey: ''))
        ->toThrow(exception: RuntimeException::class, exceptionMessage: 'La clé privée ne peut pas être vide.');
});

test(description: 'Les données cryptées contiennent IV', closure: function () {
    $result = $this->service->encrypt(data: 'test data', privateKey: 'secret key');
    $decoded = base64_decode(string: $result);
    expect($decoded)->toContain('::');
});

test(description: 'La methode CIPHER est disponible', closure: function () {
    expect(value: $this->service->getCipherMethod())
        ->toBeIn(values: openssl_get_cipher_methods());
});
