<?php

use App\DTO\MessageDTO;
use Symfony\Component\Validator\Validation;

test(description: 'Message DTO peut être créé avec un contenu null', closure: function () {
    $dto = new MessageDTO();
    $dto->setContent(content: '1');
    expect($dto->getContent())->toBe(expected: '1');
});

test(description: 'Message DTO peut être créer et récupérer avec un contenu', closure: function () {
    $dto = new MessageDTO();
    $dto->setContent(content: 'Hello World');
    expect(value: $dto->getContent())->toBe(expected: 'Hello World');
});

test(description: 'Message DTO doit échouer avec un contenu trop court', closure: function () {
    $validator = Validation::createValidatorBuilder()
        ->enableAttributeMapping()
        ->getValidator();

    $dto = new MessageDTO();
    $dto->setContent(content: '1');

    $violations = $validator->validate($dto);

    expect(value: count(value: $violations))
        ->toBe(expected: 1)
        ->and($violations[0]->getMessage())
        ->toBe(expected: 'Le message doit contenir au moins 2 caractères');
});

test(description: 'Message DTO doit être valide avec un contenu correct', closure: function () {
    $validator = Validation::createValidatorBuilder()
        ->enableAttributeMapping()
        ->getValidator();

    $dto = new MessageDTO();
    $dto->setContent(content: 'Un message valide');

    $violations = $validator->validate(value: $dto);
    expect(value: count($violations))->toBe(expected: 0);
});

test(description: 'Message DTO doit échouer avec un contenu trop long', closure: function () {
    $validator = Validation::createValidatorBuilder()
        ->enableAttributeMapping()
        ->getValidator();

    $dto = new MessageDTO();
    $dto->setContent(content: str_repeat(string: 'a', times: 1001));

    $violations = $validator->validate(value: $dto);
    expect(value: count($violations))
        ->toBe(expected: 1)
        ->and($violations[0]->getMessage())
        ->toBe(expected: 'Le message ne peut pas dépasser 1000 caractères');
});
