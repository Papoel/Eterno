<?php

declare(strict_types=1);

use App\Entity\Light;

test(description: 'Vérifier que l\'entité Light existe', closure: function (): void {
    $this->assertTrue(condition: class_exists(class: Light::class));
});

test(description: 'vérifie que la classe Light comporte les propriétés requises', closure: function () {
    $light = new Light();

    expect(value: property_exists(object_or_class: $light, property: 'id'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $light, property: 'firstname'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $light, property: 'lastname'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $light, property: 'username'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $light, property: 'birthdayAt'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $light, property: 'deceasedAt'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $light, property: 'createdAt'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $light, property: 'userAccount'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $light, property: 'messages'))->toBeTrue();
});

test(description: 'vérifie que la classe Light comporte les getters et les setters', closure: function () {
    $userReflection = new \ReflectionClass(objectOrClass: Light::class);
    $userProperties = $userReflection->getProperties();

    foreach ($userProperties as $property) {
        $propertyName = $property->getName();

        // Si les propriétés sont 'id' ou 'message', nous n'avons pas de méthode set..., donc on exclut cette vérification
        if ($propertyName === 'id' || $propertyName === 'messages') {
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

test(description: 'Vérifie que la méthode addMessage existe', closure: function () {
    $userReflection = new ReflectionClass(objectOrClass: Light::class);
    try {
        $addLightMethod = $userReflection->getMethod(name: 'addMessage');
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    expect($addLightMethod)->toBeInstanceOf(class: ReflectionMethod::class);
});

test(description: 'Vérifie que la méthode removeMessage existe', closure: function () {
    $userReflection = new ReflectionClass(objectOrClass: Light::class);
    try {
        $removeLightMethod = $userReflection->getMethod(name: 'removeMessage');
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    expect($removeLightMethod)->toBeInstanceOf(class: ReflectionMethod::class);
});
