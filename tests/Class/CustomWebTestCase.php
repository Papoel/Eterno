<?php

declare(strict_types=1);

namespace App\Tests\Class;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomWebTestCase extends WebTestCase
{
    /**
     * @return KernelBrowser
     */
    public static function createAuthenticatedClient(
        string $username = 'user1',
        string $password = 'password'
    ): KernelBrowser {
        return static::createClient();
    }
}
