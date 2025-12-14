<?php

use App\Entity\User;

test(description: 'Lorsque je crée un utilisateur, la date de création doit être définie.', closure: function (): void {
    $user = new User();
    $user->setEmail(email: 'test@email.fr');
    $user->setCreatedAt(createdAt: new DateTimeImmutable());

    expect($user->getCreatedAt())->toBeInstanceOf(DateTimeImmutable::class);

});
