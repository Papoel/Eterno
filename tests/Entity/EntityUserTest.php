<?php

declare(strict_types=1);

use App\Entity\Light;
use App\Entity\Message;
use App\Entity\User;
use App\Tests\Abstract\EntityTestCase;


test(description: 'Vérifie que la classe User existe', closure: function (): void {
    $this->assertTrue(condition: class_exists(class: User::class));
});

test(description: 'Création d\'une instance de la classe User', closure: function (): void {
    $user = new User();

    expect($user)->toBeInstanceOf(class: User::class);
});

test(description: 'vérifie que la classe User comporte les propriétés requises', closure: function () {
    $user = new User();

    expect(value: property_exists(object_or_class: $user, property: 'id'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'firstname'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'lastname'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'username'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'createdAt'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'updatedAt'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'email'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'password'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'roles'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'birthday'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'mobile'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'avatar'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'avatarFile'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'lights'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'messages'))->toBeTrue()
        ->and(value: property_exists(object_or_class: $user, property: 'invitations'))->toBeTrue();
});

test(description: 'vérifie que la classe User comporte les getters et les setters', closure: function () {
    $userReflection = new \ReflectionClass(objectOrClass: User::class);
    $userProperties = $userReflection->getProperties();

    foreach ($userProperties as $property) {
        $propertyName = $property->getName();

        // Si les propriétés sont 'id', 'lights' ou 'messages', nous n'avons pas de méthode set..., donc on
        // exclut cette vérification
        if ($propertyName === 'id' || $propertyName === 'lights' || $propertyName === 'messages' || $propertyName === 'invitations') {
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

test(description: 'Vérifie que les méthodes addLight et removeLight fonctionnent', closure: function () {
    $user = new User();
    $light = new Light();

    $user->addLight($light);
    expect($user->getLights())->toContain($light);

    $user->removeLight($light);
    expect($user->getLights())->not->toContain($light);
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

test(description: 'Un utilisateur envoie un message à une Light', closure: function () {
    $user = new User();
    $light = new Light();
    $message = new Message();

    $user->addMessage($message);
    $light->addMessage($message);

    expect($user->getMessages())->toContain($message)
        ->and($light->getMessages())->toContain($message);
});

test(description: 'Un utilisateur peut supprimer un message', closure: function () {
    $user = new User();
    $message = new Message();

    $user->addMessage($message);
    $user->removeMessage($message);

    expect($user->getMessages())->not->toContain($message);
});

test(description: 'Doit retourner le nom complet de l\'utilisateur - (email)', closure: function () {
    $user = new User();
    $user->setEmail(email: 'test@example.com');
    $user->setFirstname(firstname: 'john');
    $user->setLastname(lastname: 'doe');

    expect($user->__toString())->toBe(expected: 'John Doe - (test@example.com)')
        ->and($user->getFirstname())->toBe(expected: 'john')
        ->and($user->getLastname())->toBe(expected: 'doe');
});

test(description: 'Doit retourner le mail de l\'utilisateur si lastname est vide', closure: function () {
    $user = new User();
    $user->setFirstname(firstname: 'john');
    $user->setEmail(email: 'test@example.com');

    expect($user->__toString())->toBe(expected: 'John  - (test@example.com)');
});

test(description: 'Doit retourner le username si il est définis', closure: function () {
    $user = new User();
    $user->setUsername(username: 'killdag59');

    expect($user->getUsername())->toBe(expected: 'killdag59');
});

test(description: 'Doit retourner null quand j\'appel getId sur un utilisateur non persisté', closure: function () {
    $user = new User();

    expect($user->getId())->toBeNull();
});

test(description: 'Doit retourner l\'email de l\'utilisateur', closure: function () {
    $user = new User();
    $user->setEmail(email: 'test@example.com');

    expect($user->getEmail())->toBe(expected: 'test@example.com');
});

test(description: 'Doit enregistrer le mot de passe de l\'utilisateur', closure: function () {
    $user = new User();
    $user->setPassword(password: 'password');

    expect($user->getPassword())->toBe(expected: 'password');
});

test(description: 'Le ROLE_USER doit être ajouté par défaut', closure: function () {
    $user = new User();
    expect($user->getRoles())->toContain('ROLE_USER');
});

test(description: 'Lorsqu\'un utilisateur est créé, il doit avoir le rôle ROLE_USER en plus des rôles qu\'on lui donne', closure: function () {
    $user = new User();
    $user->setRoles(roles: ['ROLE_ADMIN']);

    expect($user->getRoles())->toContain('ROLE_USER');
});

test(description: 'Doit renvoyer l\'email de l\'utilisateur attendu lors de l\'appel à getUserIdentifier()', closure: function () {
    $user = new User();
    $user->setEmail(email: 'test@example.com');

    expect($user->getUserIdentifier())->toBe(expected: 'test@example.com');
});

test(description: 'Ne doit pas avoir d\'implémentation spécifique pour eraseCredentials()', closure: function () {
    $user = new User();
    $user->eraseCredentials();

    expect(value: null)->toBeNull();
});

test(description: 'Doit retourner au moins le rôle ROLE_USER', closure: function () {
    $user = new User();
    $user->setRoles(roles: ['ROLE_ADMIN']);

    expect($user->getRoles())->toContain(needles: 'ROLE_USER');
});

test(description: 'Doit retourner le nom complet de l\'utilisateur', closure: function () {
    $user = new User();
    $user->setFirstname(firstname: 'john');
    $user->setLastname(lastname: 'doe');

    expect($user->getFullname())->toBe(expected: 'John Doe');
});

test(description: 'Doit retourner la date d\'anniversaire de l\'utilisateur', closure: function () {
    $user = new User();
    $user->setBirthday(birthday: new DateTime());

    expect($user->getBirthday())->toBeInstanceOf(class: DateTime::class);
});

test(description: 'Doit retourner le numéro de téléphone de l\'utilisateur', closure: function () {
    $user = new User();
    $user->setMobile(mobile: '0606060606');

    expect($user->getMobile())->toBe(expected: '0606060606');
});
