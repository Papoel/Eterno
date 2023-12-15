<?php

use App\Entity\Light;
use App\Tests\ApiTestCase;

$entityName = 'Light';
$uri = '/api/' . strtolower($entityName) . 's/1';

/*test(description: 'Vérifie le type de la réponse JSON pour l\'entité ' . $entityName, closure: function () use ($entityName, $uri) {
    // Utilise la variable $entityName pour déterminer le nom de l'entité
    $testCase = new ApiTestCase(name: $entityName);

    // Utilise $entityName dans l'URI pour la requête@
    $data = $testCase->getApiResponse(uri: $uri);

    // Vérifie que le champ "@type" de la réponse JSON est une chaîne de caractères égale à $entityName
    expect(value: $data['data']['@type'])->toBeString()->toBe(expected: $entityName);
});*/

test(description: 'Vérifie le statut 200 de la réponse JSON pour l\'entité Light', closure: function () use ($entityName, $uri) {
    $testCase = new ApiTestCase(name: $entityName);
    $data = $testCase->getApiResponse(uri: $uri);

    expect(value: $data['response']->getStatusCode())->toBeInt()->toBe(expected: 200);
});

/*test(description: 'Vérifie que les champs textuels de l\'entité Light sont des chaînes de caractères', closure: function () use ($entityName, $uri) {
    $testCase = new ApiTestCase(name: $entityName);
    $data = $testCase->getApiResponse(uri: $uri);

    expect(value: $data['data']['firstname'])->toBeString()
        ->and(value: $data['data']['lastname'])->toBeString()
        ->and(value: $data['data']['username'])->toBeString();
});*/

test(description: 'Vérifie que les champs textuels de l\'entité Light respectent les contraintes de longueur', closure: function () use ($entityName, $uri,) {
    $maxFieldLength = Light::LENGTH_FIELD_MAX;
    $testCase = new ApiTestCase(name: $entityName);
    $data = $testCase->getApiResponse(uri: $uri);

    expect(value: strlen($data['data']['firstname']))->toBeInt()->toBeLessThanOrEqual($maxFieldLength)
        ->and(value: strlen($data['data']['lastname']))->toBeInt()->toBeLessThanOrEqual($maxFieldLength)
        ->and(value: strlen($data['data']['username']))->toBeInt()->toBeLessThanOrEqual($maxFieldLength);
});
