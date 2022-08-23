<?php

namespace Nwidart\Modules\Tests\Commands;

use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class ControllerMakeCommandTest extends BaseTestCase
{
    use MatchesSnapshots;
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $finder;
    /**
     * @var string
     */
    private $modulePath;

    public function setUp(): void
    {
        parent::setUp();
        $this->modulePath = base_path('modules/Blog');
        $this->finder = $this->app['files'];
        $this->artisan('module:make', ['name' => ['Blog']]);
    }

    public function tearDown(): void
    {
        $this->app[RepositoryInterface::class]->delete('Blog');
        parent::tearDown();
    }

    /** @test */
    public function itGeneratesANewControllerClass()
    {
        $code = $this->artisan('module:make-controller', ['controller' => 'MyController', 'module' => 'Blog']);

        $this->assertTrue(is_file($this->modulePath.'/Http/Controllers/MyController.php'));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectFileWithContent()
    {
        $code = $this->artisan('module:make-controller', ['controller' => 'MyController', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Http/Controllers/MyController.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itAppendsControllerToNameIfNotPresent()
    {
        $code = $this->artisan('module:make-controller', ['controller' => 'My', 'module' => 'Blog']);

        $this->assertTrue(is_file($this->modulePath.'/Http/Controllers/MyController.php'));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itAppendsControllerToClassNameIfNotPresent()
    {
        $code = $this->artisan('module:make-controller', ['controller' => 'My', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Http/Controllers/MyController.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesAPlainController()
    {
        $code = $this->artisan('module:make-controller', [
            'controller' => 'MyController',
            'module' => 'Blog',
            '--plain' => true,
        ]);

        $file = $this->finder->get($this->modulePath.'/Http/Controllers/MyController.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesAnApiController()
    {
        $code = $this->artisan('module:make-controller', [
            'controller' => 'MyController',
            'module' => 'Blog',
            '--api' => true,
        ]);

        $file = $this->finder->get($this->modulePath.'/Http/Controllers/MyController.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespace()
    {
        $this->app['config']->set('modules.paths.generator.controller.path', 'Controllers');

        $code = $this->artisan('module:make-controller', ['controller' => 'MyController', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Controllers/MyController.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespaceSpecific()
    {
        $this->app['config']->set('modules.paths.generator.controller.namespace', 'Controllers');

        $code = $this->artisan('module:make-controller', ['controller' => 'MyController', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Http/Controllers/MyController.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanGenerateAControllerInSubNamespaceInCorrectFolder()
    {
        $code = $this->artisan('module:make-controller', ['controller' => 'Api\\MyController', 'module' => 'Blog']);

        $this->assertTrue(is_file($this->modulePath.'/Http/Controllers/Api/MyController.php'));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanGenerateAControllerInSubNamespaceWithCorrectGeneratedFile()
    {
        $code = $this->artisan('module:make-controller', ['controller' => 'Api\\MyController', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Http/Controllers/Api/MyController.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }
}
