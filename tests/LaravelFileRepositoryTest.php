<?php

namespace Nwidart\Modules\Tests;

use Illuminate\Filesystem\Filesystem;
use Nwidart\Modules\Collection;
use Nwidart\Modules\Contracts\ActivatorInterface;
use Nwidart\Modules\Exceptions\InvalidAssetPath;
use Nwidart\Modules\Exceptions\ModuleNotFoundException;
use Nwidart\Modules\Laravel\LaravelFileRepository;
use Nwidart\Modules\Module;

class LaravelFileRepositoryTest extends BaseTestCase
{
    /**
     * @var LaravelFileRepository
     */
    private $repository;

    /**
     * @var ActivatorInterface
     */
    private $activator;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new LaravelFileRepository($this->app);
        $this->activator = $this->app[ActivatorInterface::class];
    }

    public function tearDown(): void
    {
        $this->activator->reset();
        parent::tearDown();
    }

    /** @test */
    public function itAddsLocationToPaths()
    {
        $this->repository->addLocation('some/path');

        $paths = $this->repository->getPaths();
        $this->assertCount(1, $paths);
        $this->assertEquals('some/path', $paths[0]);
    }

    /** @test */
    public function itReturnsACollection()
    {
        $this->repository->addLocation(__DIR__.'/stubs/valid');

        $this->assertInstanceOf(Collection::class, $this->repository->toCollection());
        $this->assertInstanceOf(Collection::class, $this->repository->collections());
    }

    /** @test */
    public function itReturnsAllEnabledModules()
    {
        $this->repository->addLocation(__DIR__.'/stubs/valid');

        $this->assertCount(0, $this->repository->getByStatus(true));
        $this->assertCount(0, $this->repository->allEnabled());
    }

    /** @test */
    public function itReturnsAllDisabledModules()
    {
        $this->repository->addLocation(__DIR__.'/stubs/valid');

        $this->assertCount(3, $this->repository->getByStatus(false));
        $this->assertCount(3, $this->repository->allDisabled());
    }

    /** @test */
    public function itCountsAllModules()
    {
        $this->repository->addLocation(__DIR__.'/stubs/valid');

        $this->assertEquals(3, $this->repository->count());
    }

    /** @test */
    public function itFindsAModule()
    {
        $this->repository->addLocation(__DIR__.'/stubs/valid');

        $this->assertInstanceOf(Module::class, $this->repository->find('recipe'));
    }

    /** @test */
    public function itFindsAModuleByAlias()
    {
        $this->repository->addLocation(__DIR__.'/stubs/valid');

        $this->assertInstanceOf(Module::class, $this->repository->findByAlias('recipe'));
        $this->assertInstanceOf(Module::class, $this->repository->findByAlias('required_module'));
    }

    /** @test */
    public function itFindOrFailThrowsExceptionIfModuleNotFound()
    {
        $this->expectException(ModuleNotFoundException::class);

        $this->repository->findOrFail('something');
    }

    /** @test */
    public function itFindsTheModuleAssetPath()
    {
        $this->repository->addLocation(__DIR__.'/stubs/valid/Recipe');
        $assetPath = $this->repository->assetPath('recipe');

        $this->assertEquals(public_path('modules/recipe'), $assetPath);
    }

    /** @test */
    public function itGetsTheUsedStoragePath()
    {
        $path = $this->repository->getUsedStoragePath();

        $this->assertEquals(storage_path('app/modules/modules.used'), $path);
    }

    /** @test */
    public function itSetsUsedModule()
    {
        $this->repository->addLocation(__DIR__.'/stubs/valid');

        $this->repository->setUsed('Recipe');

        $this->assertEquals('Recipe', $this->repository->getUsedNow());
    }

    /** @test */
    public function itReturnsLaravelFilesystem()
    {
        $this->assertInstanceOf(Filesystem::class, $this->repository->getFiles());
    }

    /** @test */
    public function itGetsTheAssetsPath()
    {
        $this->assertEquals(public_path('modules'), $this->repository->getAssetsPath());
    }

    /** @test */
    public function itGetsASpecificModuleAsset()
    {
        $path = $this->repository->asset('recipe:test.js');

        $this->assertEquals('//localhost/modules/recipe/test.js', $path);
    }

    /** @test */
    public function itThrowsExceptionIfModuleIsOmitted()
    {
        $this->expectException(InvalidAssetPath::class);
        $this->expectExceptionMessage('Module name was not specified in asset [test.js].');

        $this->repository->asset('test.js');
    }

    /** @test */
    public function itCanDetectIfModuleIsActive()
    {
        $this->repository->addLocation(__DIR__.'/stubs/valid');

        $this->repository->enable('Recipe');

        $this->assertTrue($this->repository->isEnabled('Recipe'));
    }

    /** @test */
    public function itCanDetectIfModuleIsInactive()
    {
        $this->repository->addLocation(__DIR__.'/stubs/valid');

        $this->repository->isDisabled('Recipe');

        $this->assertTrue($this->repository->isDisabled('Recipe'));
    }

    /** @test */
    public function itCanGetAndSetTheStubsPath()
    {
        $this->repository->setStubPath('some/stub/path');

        $this->assertEquals('some/stub/path', $this->repository->getStubPath());
    }

    /** @test */
    public function itGetsTheConfiguredStubsPathIfEnabled()
    {
        $this->app['config']->set('modules.stubs.enabled', true);

        $this->assertEquals(base_path('vendor/nwidart/laravel-modules/src/Commands/stubs'), $this->repository->getStubPath());
    }

    /** @test */
    public function itReturnsDefaultStubPath()
    {
        $this->assertNull($this->repository->getStubPath());
    }

    /** @test */
    public function itCanDisabledAModule()
    {
        $this->repository->addLocation(__DIR__.'/stubs/valid');

        $this->repository->disable('Recipe');

        $this->assertTrue($this->repository->isDisabled('Recipe'));
    }

    /** @test */
    public function itCanEnableAModule()
    {
        $this->repository->addLocation(__DIR__.'/stubs/valid');

        $this->repository->enable('Recipe');

        $this->assertTrue($this->repository->isEnabled('Recipe'));
    }

    /** @test */
    public function itCanDeleteAModule()
    {
        $this->artisan('module:make', ['name' => ['Blog']]);

        $this->repository->delete('Blog');

        $this->assertFalse(is_dir(base_path('modules/Blog')));
    }

    /** @test */
    public function itCanFindAllRequirementsOfAModule()
    {
        $this->repository->addLocation(__DIR__.'/stubs/valid');

        $requirements = $this->repository->findRequirements('Recipe');

        $this->assertCount(1, $requirements);
        $this->assertInstanceOf(Module::class, $requirements[0]);
    }

    /** @test */
    public function itCanRegisterMacros()
    {
        Module::macro('registeredMacro', function () {
        });

        $this->assertTrue(Module::hasMacro('registeredMacro'));
    }

    /** @test */
    public function itDoesNotHaveUnregisteredMacros()
    {
        $this->assertFalse(Module::hasMacro('unregisteredMacro'));
    }

    /** @test */
    public function itCallsMacrosOnModules()
    {
        Module::macro('getReverseName', function () {
            return strrev($this->getLowerName());
        });

        $this->repository->addLocation(__DIR__.'/stubs/valid');
        $module = $this->repository->find('recipe');

        $this->assertEquals('epicer', $module->getReverseName());
    }
}
