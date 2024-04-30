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
        $admin->setFirstname(firstname: 'Pascal');
        $admin->setUsername(username: 'Papoel');
        $admin->setEmail(email: 'admin@eterno.fr');
        $hash = $this->passwordHasher->hashPassword($admin, 'admin');
        $admin->setPassword(password: $hash);
        $admin->setRoles(roles: ['ROLE_ADMIN']);
        $admin->setBirthday(birthday: date_create('1985-02-20'));
        $admin->setMobile(mobile: '0605040302');

        $manager->persist($admin);

        // Génération des utilisateurs
        $users = [];
        for ($i = 1; $i <= 5; ++$i) {
            $user = new User();
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setUsername(ucfirst($user->getFirstname()).'-'.ucfirst($user->getLastname()));
            $user->setEmail('email'.$i.'@eterno.fr');
            $hash = $this->passwordHasher->hashPassword($user, 'password'.$i);
            $user->setPassword($hash);
            $user->setBirthday($faker->dateTimeBetween('-65 years', '-18 years'));
            $user->setMobile('0'.$faker->numberBetween(6, 7).$faker->randomNumber(8, true));

            $manager->persist($user);
            $users[] = $user;
        }

        // Génération des lumières
        $lights = [];
        for ($i = 1; $i <= 10; ++$i) {
            $light = new Light();
            $user = $faker->randomElement($users);
            $light->setUserAccount($user);
            $light->setFirstname($faker->firstName());
            $light->setLastname($faker->lastName());
            $light->setUsername(ucfirst($light->getFirstname()).'-'.ucfirst($light->getLastname()));
            $age = $faker->numberBetween(18, 65);
            $currentYear = date('Y');
            $yearOfBirth = $currentYear - $age;
            $date = $faker->dateTimeBetween($yearOfBirth.'-01-01', $yearOfBirth.'-12-31');
            $light->setBirthdayAt($date);
            $date = $faker->dateTimeBetween($light->getBirthdayAt()->format('Y-m-d'), 'now');
            $light->setDeceasedAt($date);

            $manager->persist($light);
            $lights[] = $light;
        }

        // Génération des messages
        for ($i = 1; $i <= 5000; ++$i) {
            $message = new Message();
            $sender = $faker->randomElement($users);
            $recipient = $faker->randomElement($lights);
            $message->setUserAccount($sender);
            $message->setLight($recipient);
            $content = $faker->text(1000);
            $message->setContent($this->encryptService->encrypt($content, $sender->getPassword()));

            // Utiliser DateTimeImmutable pour la date de création
            $date = $faker->dateTimeBetween(startDate: '-1 year', endDate: 'now');
            $immutableDate = \DateTimeImmutable::createFromMutable($date);
            $message->setCreatedAt($immutableDate);

            $manager->persist($message);
        }

        $manager->flush();
    }
}
