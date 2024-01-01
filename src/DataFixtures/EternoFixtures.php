<?php

namespace App\DataFixtures;

use App\Entity\Light;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EternoFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create(locale: 'fr_FR');

        /** @var User $users */
        $users = [];
        $admin = new User();
        $admin->setFirstname(firstname: 'pascal');
        $admin->setLastname(lastname: 'briffard');
        $admin->setUsername(username: 'papoel');
        $admin->setBirthday(birthday: new \DateTime(datetime: '1985-02-20'));
        $admin->setMobile(mobile: '0766345110');
        $admin->setEmail(email: 'admin@eterno.fr');
        $hash = $this->passwordHasher->hashPassword(user: $admin, plainPassword: 'admin');
        $admin->setPassword(password: $hash);
        $admin->setRoles(roles: ['ROLE_ADMIN']);

        $manager->persist($admin);
        $users[] = $admin;

        /* Génération de 5 utilisateurs */
        for ($i = 1; $i <= 5; ++$i) {
            $user = new User();
            $user->setFirstname(firstname: $faker->firstName());
            $user->setLastname(lastname: $faker->lastName());
            $user->setUsername(username: ucfirst($user->getFirstname()).'-'.ucfirst($user->getLastname()));
            $user->setEmail(email: 'email'.$i.'@eterno.fr');
            $hash = $this->passwordHasher->hashPassword(user: $user, plainPassword: 'password'.$i);
            $user->setPassword(password: $hash);
            $user->setBirthday(birthday: $faker->dateTimeBetween(startDate: '-65 years', endDate: '-18 years'));
            // Fake phone number format (FR) starting with 06 or 07
            $user->setMobile(mobile: '0'.$faker->numberBetween(int1: 6, int2: 7).$faker->randomNumber(nbDigits: 8, strict: true));

            $manager->persist($user);
            $users[] = $user;
        }

        /** Génération de 10 Lights */
        $lights = [];
        for ($i = 1; $i <= 10; ++$i) {
            $light = new Light();
            $user = $faker->randomElement($users);

            // Vérifie si l'utilisateur a déjà des Lights.
            $existingLights = $user->getLights();

            // Si l'utilisateur a déjà un Light, ajoute aléatoirement un Light à un autre utilisateur.
            if (!empty($existingLights) && $faker->boolean(chanceOfGettingTrue: 50)) {
                $usersWithoutLights = array_diff($users, [$user]);
                $otherUser = $faker->randomElement($usersWithoutLights);
                $light->setUserAccount($otherUser);
            } else {
                $light->setUserAccount($user);
            }

            $light->setFirstname($faker->firstName());
            $light->setLastname($faker->lastName());
            $light->setUsername(username: ucfirst($light->getFirstname()).'-'.ucfirst($light->getLastname()));

            $age = $faker->numberBetween(int1: 18, int2: 65);
            $currentYear = date(format: 'Y');
            $yearOfBirth = $currentYear - $age;
            $date = $faker->dateTimeBetween(startDate: $yearOfBirth.'-01-01', endDate: $yearOfBirth.'-12-31');
            $light->setBirthdayAt($date);

            $date = $faker->dateTimeBetween($light->getBirthdayAt()->format(format: 'Y-m-d'), endDate: 'now');
            $light->setDeceasedAt($date);

            $manager->persist($light);
            $lights[] = $light;
        }

        /* Générer des messages de User vers Light */
        for ($i = 1; $i <= 150; ++$i) {
            $message = new Message();
            $sender = $faker->randomElement($users);

            // Sélectionne aléatoirement un destinataire (Light) à chaque itération.
            $recipient = $faker->randomElement($lights);

            // Assure-toi que le message est envoyé par un utilisateur vers un light.
            $message->setUserAccount(userAccount: $sender);
            $message->setLight(light: $recipient);
            $message->setMessage(message: $faker->text(maxNbChars: 200));

            $date = $faker->dateTimeBetween(startDate: '-6 months', endDate: 'yesterday');
            $immutable = \DateTimeImmutable::createFromMutable($date);
            $message->setCreatedAt($immutable);

            $manager->persist($message);
        }
        $manager->flush();
    }
}
