<?php

use App\Entity\User;

test(description: 'Lorsque je crée un utilisateur, la date de création doit être définie.', closure: function () {
    $user = new User();
    $user->setEmail(email: 'test@email.fr');
    $user->setCreatedAt('now');

    expect($user->getCreatedAt())->toBeInstanceOf(DateTimeImmutable::class);

});
