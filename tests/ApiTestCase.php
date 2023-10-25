<?php

declare(strict_types=1);

namespace App\Tests;

use App\Kernel;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTestCase extends BaseTestCase
{
    public function json(
        string $method = Request::METHOD_GET,
        string $uri = '/',
        array  $data = [],
        array  $headers = []
    ): Response
    {
        $kernel = new Kernel($_ENV['APP_ENV'], (bool)$_ENV['APP_DEBUG']);
        $request = Request::create($uri, $method, $data, [], [], $headers);
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);
        $body = $response->getContent();

        return new Response(content: $body, status: 200, headers: []);
    }

    public function getApiResponse(string $uri, string $method = Request::METHOD_GET, array $data = []): array
    {
        $response = $this->json(method: $method, uri: $uri, data: $data);
        $body = $response->getContent();
        $data = json_decode(json: $body, associative: true, depth: 512, flags: JSON_THROW_ON_ERROR);

        return ['data' => $data, 'response' => $response];
    }

    public function assertApiType(string $uri, string $expectedType, string $method = Request::METHOD_GET, array $data = []): void
    {
        $data = $this->getApiResponse($uri, $method, $data);
        $this->assertEquals($expectedType, $data['data']['@type']);
    }

    public function assertApiStatusCode(string $uri, int $expectedStatusCode, string $method = Request::METHOD_GET, array $data = []): void
    {
        $data = $this->getApiResponse($uri, $method, $data);
        $this->assertEquals($expectedStatusCode, $data['response']->getStatusCode());
    }
}
