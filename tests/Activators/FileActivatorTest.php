<?php

namespace Nwidart\Modules\Tests\Activators;

use Nwidart\Modules\Activators\FileActivator;
use Nwidart\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class FileActivatorTest extends BaseTestCase
{
    use MatchesSnapshots;

    /**
     * @var TestModule
     */
    private $module;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $finder;

    /**
     * @var FileActivator
     */
    private $activator;

    public function setUp(): void
    {
        parent::setUp();
        $this->module = new TestModule($this->app, 'Recipe', __DIR__.'/stubs/valid/Recipe');
        $this->finder = $this->app['files'];
        $this->activator = new FileActivator($this->app);
    }

    public function tearDown(): void
    {
        $this->activator->reset();
        parent::tearDown();
    }

    /** @test */
    public function itCreatesValidJsonFileAfterEnabling()
    {
        $this->activator->enable($this->module);
        $this->assertMatchesSnapshot($this->finder->get($this->activator->getStatusesFilePath()));

        $this->activator->setActive($this->module, true);
        $this->assertMatchesSnapshot($this->finder->get($this->activator->getStatusesFilePath()));
    }

    /** @test */
    public function itCreatesValidJsonFileAfterDisabling()
    {
        $this->activator->disable($this->module);
        $this->assertMatchesSnapshot($this->finder->get($this->activator->getStatusesFilePath()));

        $this->activator->setActive($this->module, false);
        $this->assertMatchesSnapshot($this->finder->get($this->activator->getStatusesFilePath()));
    }

    /** @test */
    public function itCanCheckModuleEnabledStatus()
    {
        $this->activator->enable($this->module);
        $this->assertTrue($this->activator->hasStatus($this->module, true));

        $this->activator->setActive($this->module, true);
        $this->assertTrue($this->activator->hasStatus($this->module, true));
    }

    /** @test */
    public function itCanCheckModuleDisabledStatus()
    {
        $this->activator->disable($this->module);
        $this->assertTrue($this->activator->hasStatus($this->module, false));

        $this->activator->setActive($this->module, false);
        $this->assertTrue($this->activator->hasStatus($this->module, false));
    }

    /** @test */
    public function itCanCheckStatusOfModuleThatHasntBeenEnabledOrDisabled()
    {
        $this->assertTrue($this->activator->hasStatus($this->module, false));
    }
}

class TestModule extends \Nwidart\Modules\Laravel\Module
{
    public function registerProviders(): void
    {
        parent::registerProviders();
    }
}
