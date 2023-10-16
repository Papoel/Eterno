<?php

declare(strict_types=1);

use App\Entity\User;

test(description: 'Vérifie que la classe User existe', closure: function (): void {
    $this->assertTrue(condition: class_exists(class: User::class));
});

test(description: 'vérifie que la classe User comporte les propriétés requises', closure: function () {
    $user = new User();

    expect(value: property_exists(object_or_class: $user, property: 'id'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'firstname'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'lastname'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'username'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'email'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'password'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'createdAt'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'roles'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'lights'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'messages'))->toBeTrue();
});

test(description: 'vérifie que la classe User comporte les getters et les setters', closure: function () {
    $userReflection = new \ReflectionClass(objectOrClass: User::class);
    $userProperties = $userReflection->getProperties();

    foreach ($userProperties as $property) {
        $propertyName = $property->getName();

        // Si les propriétés sont 'id', 'lights' ou 'messages', nous n'avons pas de méthode set..., donc on
        // exclut cette vérification
        if ($propertyName === 'id' || $propertyName === 'lights' || $propertyName === 'messages') {
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

test(description: 'Vérifie que la méthode addLight existe', closure: function () {
    $userReflection = new ReflectionClass(objectOrClass: User::class);
    try {
        $addLightMethod = $userReflection->getMethod(name: 'addLight');
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    expect($addLightMethod)->toBeInstanceOf(class: ReflectionMethod::class);
});

test(description: 'Vérifie que la méthode removeLight existe', closure: function () {
    $userReflection = new ReflectionClass(objectOrClass: User::class);
    try {
        $removeLightMethod = $userReflection->getMethod(name: 'removeLight');
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    expect($removeLightMethod)->toBeInstanceOf(class: ReflectionMethod::class);
});

test(description: 'Vérifie que la méthode addMessage existe', closure: function () {
    $userReflection = new ReflectionClass(objectOrClass: User::class);
    try {
        $addLightMethod = $userReflection->getMethod(name: 'addMessage');
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    expect($addLightMethod)->toBeInstanceOf(class: ReflectionMethod::class);
});

test(description: 'Vérifie que la méthode removeMessage existe', closure: function () {
    $userReflection = new ReflectionClass(objectOrClass: User::class);
    try {
        $removeLightMethod = $userReflection->getMethod(name: 'removeMessage');
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    expect($removeLightMethod)->toBeInstanceOf(class: ReflectionMethod::class);
});
