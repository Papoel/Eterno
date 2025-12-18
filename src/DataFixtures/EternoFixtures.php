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
    private const BATCH_SIZE = 20;

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
        $hash = $this->passwordHasher->hashPassword(user: $admin, plainPassword: 'Admin1234');
        $admin->setPassword(password: $hash);
        $admin->setRoles(roles: ['ROLE_ADMIN']);
        $admin->setBirthday(birthday: date_create(datetime: '1985-02-20'));
        $admin->setMobile(mobile: '0605040302');

        $manager->persist($admin);
        $manager->flush();

        $adminId = $admin->getId();
        $adminPassword = $admin->getPassword();
        $manager->clear();

        // Génération des utilisateurs
        $userIds = [];
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
            $manager->flush();

            $userIds[$i] = [
                'id' => $user->getId(),
                'password' => $user->getPassword(),
            ];

            $manager->clear();
        }

        // Générer 5 lumières pour l'admin
        $adminLightIds = [];
        for ($i = 1; $i <= 5; ++$i) {
            $admin = $manager->find(User::class, $adminId);
            $adminLightIds[] = $this->createLight($admin, $faker, $manager);
            $manager->clear();
        }

        // Génération des lumières Users
        $lightIds = [];
        for ($i = 1; $i <= 10; ++$i) {
            $userInfo = $faker->randomElement($userIds);
            $user = $manager->find(User::class, $userInfo['id']);
            $lightIds[] = $this->createLight($user, $faker, $manager);
            $manager->clear();
        }

        // Générer messages pour les lumières de l'admin
        foreach ($adminLightIds as $lightInfo) {
            $messageCount = $faker->numberBetween(int1: 10, int2: 500);

            for ($i = 1; $i <= $messageCount; ++$i) {
                $admin = $manager->find(User::class, $adminId);
                $light = $manager->find(Light::class, $lightInfo['id']);

                $message = new Message();
                $message->setUserAccount(userAccount: $admin);
                $message->setLight(light: $light);
                $content = $faker->text(maxNbChars: 500); // Réduit à 500 chars
                $message->setContent(content: $this->encryptService->encrypt(data: $content, privateKey: $adminPassword));

                // Date entre le décès + 1 jour et aujourd'hui
                $startDate = (new \DateTime($lightInfo['deceasedAt']))->modify('+1 day');
                $date = $faker->dateTimeBetween(startDate: $startDate, endDate: 'now');
                $immutableDate = \DateTimeImmutable::createFromMutable($date);
                $message->setCreatedAt(createdAt: $immutableDate);

                $manager->persist($message);

                if (0 === $i % self::BATCH_SIZE) {
                    $manager->flush();
                    $manager->clear();
                }
            }

            $manager->flush();
            $manager->clear();
        }

        // Générer messages Users
        for ($i = 1; $i <= 500; ++$i) {
            $userInfo = $faker->randomElement($userIds);
            $lightInfo = $faker->randomElement($lightIds);

            $sender = $manager->find(User::class, $userInfo['id']);
            $recipient = $manager->find(Light::class, $lightInfo['id']);

            $message = new Message();
            $message->setUserAccount(userAccount: $sender);
            $message->setLight(light: $recipient);
            $content = $faker->text(maxNbChars: 500); // Réduit à 500 chars
            $message->setContent(content: $this->encryptService->encrypt(data: $content, privateKey: $userInfo['password']));

            // Date entre le décès + 1 jour et aujourd'hui
            $startDate = (new \DateTime($lightInfo['deceasedAt']))->modify('+1 day');
            $date = $faker->dateTimeBetween(startDate: $startDate, endDate: 'now');
            $immutableDate = \DateTimeImmutable::createFromMutable($date);
            $message->setCreatedAt(createdAt: $immutableDate);

            $manager->persist($message);

            if (0 === $i % self::BATCH_SIZE) {
                $manager->flush();
                $manager->clear();
            }
        }

        $manager->flush();
        $manager->clear();
    }

    private function createLight(User $user, \Faker\Generator $faker, ObjectManager $manager): array
    {
        $light = new Light();
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
        $manager->flush();

        return [
            'id' => $light->getId(),
            'deceasedAt' => $light->getDeceasedAt()->format('Y-m-d'),
        ];
    }
}
