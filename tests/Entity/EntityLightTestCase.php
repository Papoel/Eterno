<?php

declare(strict_types=1);

use App\Entity\Light;
use App\Entity\Message;
use App\Entity\User;

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

test(description: 'Doit retourner null quand j\'appel getId sur une Light non persisté', closure: function () {
    $light = new Light();

    expect($light->getId())->toBeNull();
});

test(description: 'Doit retourner le firstname par défaut', closure: function () {
    $light = new Light();
    $light->setFirstname(firstname: 'kevin');

    expect($light->__toString())->toBe(expected: 'Kevin');

});

test(description: 'Un message peut être ajouté à une Light', closure: function () {
    $light = new Light();
    $message = new Message();

    $light->addMessage($message);

    expect($light->getMessages())->toContain($message)
        ->and($message->getLight())->toBe($light);
});

test(description: 'Un message peut être retiré d\'une Light', closure: function () {
    $light = new Light();
    $message = new Message();

    $light->addMessage($message);
    $light->removeMessage($message);

    expect($light->getMessages())->not->toContain($message);
});

test(description: 'Un utilisateur peut être associé à une Light', closure: function () {
    $light = new Light();
    $user = new User();

    $light->setUserAccount($user);

    expect($light->getUserAccount())->toBe($user);
});

test(description: 'La date de naissance d\'une Light peut être définie', closure: function () {
    $light = new Light();
    $birthday = new \DateTime(datetime: '1982-09-21');

    $light->setBirthdayAt($birthday);

    expect($light->getBirthdayAt())->toBe($birthday);
});

test(description: 'La date de décès d\'une Light peut être définie', closure: function () {
    $light = new Light();
    $deceased = new \DateTime(datetime: '2023-09-06');

    $light->setDeceasedAt($deceased);

    expect($light->getDeceasedAt())->toBe($deceased);
});
