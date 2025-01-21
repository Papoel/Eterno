<?php

use App\DTO\MessageDTO;
use App\Entity\Light;
use App\Entity\User;
use App\Repository\LightRepository;
use App\Repository\MessageRepository;
use App\Services\Encryption\MessageEncryptionService;
use App\Services\Message\MessageManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

beforeEach(closure: function () {
    $this->entityManager = Mockery::mock(EntityManagerInterface::class);
    $this->messageRepository = Mockery::mock(MessageRepository::class);
    $this->lightRepository = Mockery::mock(LightRepository::class);
    $this->encryptionService = Mockery::mock(MessageEncryptionService::class);
    $this->csrfTokenManager = Mockery::mock(CsrfTokenManagerInterface::class);

    $this->manager = new MessageManagerService(
        $this->entityManager,
        $this->messageRepository,
        $this->lightRepository,
        $this->encryptionService,
        $this->csrfTokenManager
    );
});

test(description: 'create message persists encrypted message', closure: function () {
    $dto = new MessageDTO();
    $dto->setContent('Hello');

    $user = Mockery::mock(User::class);
    $user->shouldReceive('getPassword')->andReturn('password');

    $light = new Light();
    $this->lightRepository
        ->shouldReceive('find')
        ->with('receiver-id')
        ->andReturn($light);

    $this->encryptionService
        ->shouldReceive('encrypt')
        ->with('Hello', 'password')
        ->andReturn('encrypted-content');

    $this->entityManager->shouldReceive('persist')->once();
    $this->entityManager->shouldReceive('flush')->once();

    $this->manager->createMessage(dto: $dto, user: $user, receiverId: 'receiver-id');
});
