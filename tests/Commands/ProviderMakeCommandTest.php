<?php

namespace Nwidart\Modules\Tests\Commands;

use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class ProviderMakeCommandTest extends BaseTestCase
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
        $this->artisan('module:make', ['name' => ['Blog'], '--plain' => true]);
    }

    public function tearDown(): void
    {
        $this->app[RepositoryInterface::class]->delete('Blog');
        parent::tearDown();
    }

    /** @test */
    public function itGeneratesAServiceProvider()
    {
        $code = $this->artisan('module:make-provider', ['name' => 'MyBlogServiceProvider', 'module' => 'Blog']);

        $this->assertTrue(is_file($this->modulePath.'/Providers/MyBlogServiceProvider.php'));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectFileWithContent()
    {
        $code = $this->artisan('module:make-provider', ['name' => 'MyBlogServiceProvider', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Providers/MyBlogServiceProvider.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesAMasterServiceProviderWithResourceLoading()
    {
        $code = $this->artisan('module:make-provider', ['name' => 'BlogServiceProvider', 'module' => 'Blog', '--master' => true]);

        $file = $this->finder->get($this->modulePath.'/Providers/BlogServiceProvider.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanHaveCustomMigrationResourcesLocationPaths()
    {
        $this->app['config']->set('modules.paths.generator.migration', 'migrations');
        $code = $this->artisan('module:make-provider', ['name' => 'BlogServiceProvider', 'module' => 'Blog', '--master' => true]);

        $file = $this->finder->get($this->modulePath.'/Providers/BlogServiceProvider.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespace()
    {
        $this->app['config']->set('modules.paths.generator.provider.path', 'SuperProviders');

        $code = $this->artisan('module:make-provider', ['name' => 'BlogServiceProvider', 'module' => 'Blog', '--master' => true]);

        $file = $this->finder->get($this->modulePath.'/SuperProviders/BlogServiceProvider.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespaceSpecific()
    {
        $this->app['config']->set('modules.paths.generator.provider.namespace', 'SuperProviders');

        $code = $this->artisan('module:make-provider', ['name' => 'BlogServiceProvider', 'module' => 'Blog', '--master' => true]);

        $file = $this->finder->get($this->modulePath.'/Providers/BlogServiceProvider.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }
}
