<?php

namespace App\DataFixtures;

use App\Entity\Light;
use App\Entity\Message;
use App\Entity\User;
use App\Services\Encryptor\EncryptService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EternoFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EncryptService $encryptService,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create(locale: 'fr_FR');
        // Génération de l'administrateur
        $admin = new User();
        $admin->setFirstname(firstname: 'Bruce');
        $admin->setLastname(lastname: 'Wayne');
        $admin->setUsername(username: 'Batman');
        $admin->setEmail(email: 'bruce.wayne@gotham.city');
        $hash = $this->passwordHasher->hashPassword(user: $admin, plainPassword: 'admin');
        $admin->setPassword(password: $hash);
        $admin->setRoles(roles: ['ROLE_ADMIN']);
        $admin->setBirthday(birthday: date_create(datetime: '1985-02-20'));
        $admin->setMobile(mobile: '0605040302');

        $manager->persist($admin);

        // Génération des utilisateurs
        $users = [];
        for ($i = 1; $i <= 5; ++$i) {
            $user = new User();
            $user->setFirstname(firstname: $faker->firstName());
            $user->setLastname(lastname: $faker->lastName());
            $user->setUsername(username: ucfirst($user->getFirstname()).'-'.ucfirst($user->getLastname()));
            $user->setEmail(email: 'email'.$i.'@eterno.fr');
            $hash = $this->passwordHasher->hashPassword(user: $user, plainPassword: 'password'.$i);
            $user->setPassword(password: $hash);
            $user->setBirthday(birthday: $faker->dateTimeBetween(startDate: '-65 years', endDate: '-18 years'));
            $user->setMobile(mobile: '0'.$faker->numberBetween(int1: 6, int2: 7).$faker->randomNumber(nbDigits: 8, strict: true));

            $manager->persist($user);
            $users[] = $user;
        }

        // Génération des lumières
        $lights = [];
        for ($i = 1; $i <= 10; ++$i) {
            $light = new Light();
            $user = $faker->randomElement($users);
            $light->setUserAccount(userAccount: $user);
            $light->setFirstname(firstname: $faker->firstName());
            $light->setLastname(lastname: $faker->lastName());
            $light->setUsername(username: ucfirst(string: $light->getFirstname()).'-'.ucfirst($light->getLastname()));
            $age = $faker->numberBetween(int1: 18, int2: 65);
            $currentYear = date(format: 'Y');
            $yearOfBirth = $currentYear - $age;
            $date = $faker->dateTimeBetween(startDate: $yearOfBirth.'-01-01', endDate: $yearOfBirth.'-12-31');
            $light->setBirthdayAt(birthdayAt: $date);
            $date = $faker->dateTimeBetween($light->getBirthdayAt()->format(format: 'Y-m-d'));
            $light->setDeceasedAt(deceasedAt: $date);

            $manager->persist($light);
            $lights[] = $light;
        }

        // Génération des messages
        for ($i = 1; $i <= 5000; ++$i) {
            $message = new Message();
            $sender = $faker->randomElement($users);
            $recipient = $faker->randomElement($lights);
            $message->setUserAccount(userAccount: $sender);
            $message->setLight(light: $recipient);
            $content = $faker->text(maxNbChars: 1000);
            $message->setContent(content: $this->encryptService->encrypt(data: $content, privateKey: $sender->getPassword()));

            // Utiliser DateTimeImmutable pour la date de création
            $date = $faker->dateTimeBetween(startDate: '-1 year');
            $immutableDate = \DateTimeImmutable::createFromMutable($date);
            $message->setCreatedAt(createdAt: $immutableDate);

            $manager->persist($message);
        }

        $manager->flush();
    }
}
