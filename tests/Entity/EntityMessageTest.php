<?php

declare(strict_types=1);

use App\Entity\Message;

test(description: 'Vérifier que l\'entité Message existe', closure: function (): void {
    $this->assertTrue(condition: class_exists(class: Message::class));
});

test(description: 'vérifie que la classe Message comporte les propriétés requises', closure: function (): void {
    $message = new Message();

    expect(value: property_exists(object_or_class: $message, property: 'id'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $message, property: 'content'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $message, property: 'createdAt'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $message, property: 'userAccount'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $message, property: 'light'))->toBeTrue();
});

test(description: 'vérifie que la classe User comporte les getters et les setters', closure: function (): void {
    $userReflection = new \ReflectionClass(objectOrClass: Message::class);
    $userProperties = $userReflection->getProperties();

    foreach ($userProperties as $property) {
        $propertyName = $property->getName();

        // Si la propriété est 'id', nous n'avons pas de méthode set..., donc on exclut cette vérification
        if ($propertyName === 'id') {
            continue;
        }

        $getterMethod = 'get' . ucfirst($propertyName);
        $setterMethod = 'set' . ucfirst($propertyName);

        expect($userReflection->hasMethod($getterMethod))
            ->toBeTrue()
            ->and($userReflection->hasMethod($setterMethod))
            ->toBeTrue();
    }
});

test(description: 'Doit retourner null quand j\'appel getId sur une Light non persisté', closure: function (): void {
    $message = new Message();

    expect($message->getId())->toBeNull();
});

test(description: 'Un message peut être ajouté', closure: function (): void {
    $message = new Message();

    $message->setContent('Mon message');

    expect($message->getContent())->toBe('Mon message');
});
