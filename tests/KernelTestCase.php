<?php

declare(strict_types=1);

namespace App\Tests;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as BaseKernelTestCase;

class KernelTestCase extends BaseKernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
