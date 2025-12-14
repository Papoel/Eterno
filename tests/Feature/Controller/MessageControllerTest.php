<?php

namespace Tests\Feature\Controller;

use App\Entity\Light;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\LightRepository;
use App\Repository\MessageRepository;
use App\Services\Encryption\MessageEncryptionService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

uses(WebTestCase::class);

beforeEach(function (): void {
    $this->client = static::createClient();
    $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    $this->userRepository = static::getContainer()->get('doctrine')->getRepository(User::class);
    $this->lightRepository = static::getContainer()->get('doctrine')->getRepository(Light::class);

    // Nettoyer la base de données
    $this->entityManager->createQuery('DELETE FROM App\Entity\Message')->execute();
    $this->entityManager->createQuery('DELETE FROM App\Entity\Light')->execute();
    $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
});

test(description: 'la page de nouveau message nécessite une authentification', closure: function (): void {
    $this->client->request('GET', '/communication/new/some-uuid');

    expect(value: $this->client
        ->getResponse()
        ->getStatusCode())
        ->toBe(expected: Response::HTTP_FOUND)
        ->and(value: $this->client->getResponse()->headers->get('Location'))->toContain('/login');
});

test(description: 'un utilisateur authentifié peut accéder à la page de nouveau message', closure: function (): void {
    $testUser = new User();
    $testUser->setEmail(email: 'test1@example.com');
    $testUser->setPassword(password: 'password');
    $testUser->setFirstname(firstname: 'Test');
    $this->entityManager->persist($testUser);

    $light = new Light();
    $light->setFirstname(firstname: 'Test Light');
    $light->setUserAccount(userAccount: $testUser);
    $this->entityManager->persist($light);

    $this->entityManager->flush();

    $this->client->loginUser($testUser);

    $this->client->request('GET', '/communication/new/' . $light->getId());

    expect($this->client->getResponse()->getStatusCode())->toBe(expected: Response::HTTP_OK);
});

test(description: 'un message ne peut pas être envoyé avec un contenu vide', closure: function (): void {
    $testUser = new User();
    $testUser->setEmail(email: 'test2@example.com');
    $testUser->setPassword(password: 'password');
    $testUser->setFirstname(firstname: 'Test');
    $this->entityManager->persist($testUser);

    $light = new Light();
    $light->setFirstname(firstname: 'Test Light');
    $light->setUserAccount(userAccount: $testUser);
    $this->entityManager->persist($light);

    $this->entityManager->flush();

    $this->client->loginUser($testUser);

    $crawler = $this->client->request('POST', '/communication/new/' . $light->getId(), [
        'message' => [
            'content' => ''
        ]
    ]);

    expect($this->client->getResponse()
        ->getStatusCode())
        ->toBe(expected: Response::HTTP_UNPROCESSABLE_ENTITY)
        ->and($crawler->filter('.invalid-feedback')->text())
        ->toContain('Le message ne peut pas être vide');
});

test(description: 'un message valide est correctement enregistré et chiffré', closure: function (): void {
    $testUser = new User();
    $testUser->setEmail(email: 'test3@example.com');
    $testUser->setPassword(password: 'password');
    $testUser->setFirstname(firstname: 'Test');
    $this->entityManager->persist($testUser);

    $light = new Light();
    $light->setFirstname(firstname: 'Test Light');
    $light->setUserAccount(userAccount: $testUser);
    $this->entityManager->persist($light);

    $this->entityManager->flush();

    $this->client->loginUser($testUser);

    $messageContent = 'Ceci est un message de test';

    $crawler = $this->client->request('GET', '/communication/new/' . $light->getId());
    $form = $crawler->filter('button[aria-label="send-message"]')->form();
    $form['message[content]'] = $messageContent;
    $this->client->submit($form);

    expect(value: $this->client->getResponse()->isRedirect())->toBeTrue();

    $messageRepository = static::getContainer()->get(MessageRepository::class);
    $encryptionService = static::getContainer()->get(MessageEncryptionService::class);

    $message = $messageRepository->findOneBy(['light' => $light]);

    expect($message)
        ->toBeInstanceOf(class: Message::class)
        ->and(value: $encryptionService->decrypt(
            $message->getContent(),
            $testUser->getPassword())
        )->toBe(expected: $messageContent);
});
