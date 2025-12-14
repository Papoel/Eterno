<?php

use App\Tests\Class\CustomWebTestCase;
use Symfony\Component\HttpFoundation\Response;

test(description: 'l\'url doit retourner un code 200', closure: function (string $url): void {
    $client = CustomWebTestCase::createAuthenticatedClient();
    $client->request(method: 'GET', uri: $url);

    $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK, message: sprintf('La route %s URL est correctement chargée.', $url));
    $this->assertResponseIsSuccessful(sprintf('La route %s URL est correctement chargée.', $url));
})->with(static function (): ?\Generator {
    yield ['/'];
    yield ['/login'];
});

test(description: 'l\'url doit retourner un code 302', closure: function (string $url): void {
    $client = static::createClient();
    $client->request('GET', $url);

    $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FOUND, message: sprintf('La route %s URL est correctement chargée.', $url));
})->with(static function (): ?\Generator {
    yield ['/logout'];
});
