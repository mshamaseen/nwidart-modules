<?php

namespace Nwidart\Modules\Tests\Commands;

use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class RouteProviderMakeCommandTest extends BaseTestCase
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
    public function itGeneratesANewServiceProviderClass()
    {
        $path = $this->modulePath.'/Providers/RouteServiceProvider.php';
        $this->finder->delete($path);
        $code = $this->artisan('module:route-provider', ['module' => 'Blog']);

        $this->assertTrue(is_file($path));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectFileWithContent()
    {
        $path = $this->modulePath.'/Providers/RouteServiceProvider.php';
        $this->finder->delete($path);
        $code = $this->artisan('module:route-provider', ['module' => 'Blog']);

        $file = $this->finder->get($path);

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespace()
    {
        $this->app['config']->set('modules.paths.generator.provider.path', 'SuperProviders');

        $code = $this->artisan('module:route-provider', ['module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/SuperProviders/RouteServiceProvider.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespaceSpecific()
    {
        $this->app['config']->set('modules.paths.generator.provider.namespace', 'SuperProviders');

        $path = $this->modulePath.'/Providers/RouteServiceProvider.php';
        $this->finder->delete($path);
        $code = $this->artisan('module:route-provider', ['module' => 'Blog']);

        $file = $this->finder->get($path);

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanOverwriteRouteFileNames()
    {
        $this->app['config']->set('modules.stubs.files.routes/web', 'SuperRoutes/web.php');
        $this->app['config']->set('modules.stubs.files.routes/api', 'SuperRoutes/api.php');

        $code = $this->artisan('module:route-provider', ['module' => 'Blog', '--force' => true]);

        $file = $this->finder->get($this->modulePath.'/Providers/RouteServiceProvider.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanOverwriteFile(): void
    {
        $this->artisan('module:route-provider', ['module' => 'Blog']);
        $this->app['config']->set('modules.stubs.files.routes/web', 'SuperRoutes/web.php');

        $code = $this->artisan('module:route-provider', ['module' => 'Blog', '--force' => true]);
        $file = $this->finder->get($this->modulePath.'/Providers/RouteServiceProvider.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheCustomControllerNamespace(): void
    {
        $this->app['config']->set('modules.paths.generator.controller.path', 'Base/Http/Controllers');
        $this->app['config']->set('modules.paths.generator.provider.path', 'Base/Providers');

        $code = $this->artisan('module:route-provider', ['module' => 'Blog']);
        $file = $this->finder->get($this->modulePath.'/Base/Providers/RouteServiceProvider.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }
}
