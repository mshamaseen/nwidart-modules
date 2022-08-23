<?php

namespace Nwidart\Modules\Tests;

use Nwidart\Modules\Contracts\ActivatorInterface;
use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Exceptions\InvalidActivatorClass;

class LaravelModulesServiceProviderTest extends BaseTestCase
{
    /** @test */
    public function itBindsModulesKeyToRepositoryClass()
    {
        $this->assertInstanceOf(RepositoryInterface::class, app(RepositoryInterface::class));
        $this->assertInstanceOf(RepositoryInterface::class, app('modules'));
    }

    /** @test */
    public function itBindsActivatorToActivatorClass()
    {
        $this->assertInstanceOf(ActivatorInterface::class, app(ActivatorInterface::class));
    }

    /** @test */
    public function itThrowsExceptionIfConfigIsInvalid()
    {
        $this->expectException(InvalidActivatorClass::class);

        $this->app['config']->set('modules.activators.file', ['class' => null]);

        $this->assertInstanceOf(ActivatorInterface::class, app(ActivatorInterface::class));
    }
}
