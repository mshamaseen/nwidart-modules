<?php

namespace Nwidart\Modules\Tests\Commands;

use Nwidart\Modules\Contracts\ActivatorInterface;
use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class TestMakeCommandTest extends BaseTestCase
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

    /**
     * @var ActivatorInterface
     */
    private $activator;

    public function setUp(): void
    {
        parent::setUp();
        $this->modulePath = base_path('modules/Blog');
        $this->finder = $this->app['files'];
        $this->artisan('module:make', ['name' => ['Blog']]);
        $this->activator = $this->app[ActivatorInterface::class];
    }

    public function tearDown(): void
    {
        $this->app[RepositoryInterface::class]->delete('Blog');
        $this->activator->reset();
        parent::tearDown();
    }

    /** @test */
    public function itGeneratesANewTestClass()
    {
        $this->artisan('module:make-test', ['name' => 'EloquentPostRepositoryTest', 'module' => 'Blog']);
        $code = $this->artisan('module:make-test', ['name' => 'EloquentPostRepositoryTest', 'module' => 'Blog', '--feature' => true]);

        $this->assertTrue(is_file($this->modulePath.'/Tests/Unit/EloquentPostRepositoryTest.php'));
        $this->assertTrue(is_file($this->modulePath.'/Tests/Feature/EloquentPostRepositoryTest.php'));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectUnitFileWithContent()
    {
        $code = $this->artisan('module:make-test', ['name' => 'EloquentPostRepositoryTest', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Tests/Unit/EloquentPostRepositoryTest.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectFeatureFileWithContent()
    {
        $code = $this->artisan('module:make-test', ['name' => 'EloquentPostRepositoryTest', 'module' => 'Blog', '--feature' => true]);

        $file = $this->finder->get($this->modulePath.'/Tests/Feature/EloquentPostRepositoryTest.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultUnitNamespace()
    {
        $this->app['config']->set('modules.paths.generator.test.path', 'SuperTests/Unit');

        $code = $this->artisan('module:make-test', ['name' => 'EloquentPostRepositoryTest', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/SuperTests/Unit/EloquentPostRepositoryTest.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultUnitNamespaceSpecific()
    {
        $this->app['config']->set('modules.paths.generator.test.namespace', 'SuperTests\\Unit');

        $code = $this->artisan('module:make-test', ['name' => 'EloquentPostRepositoryTest', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Tests/Unit/EloquentPostRepositoryTest.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultFeatureNamespace()
    {
        $this->app['config']->set('modules.paths.generator.test-feature.path', 'SuperTests/Feature');

        $code = $this->artisan('module:make-test', ['name' => 'EloquentPostRepositoryTest', 'module' => 'Blog', '--feature' => true]);

        $file = $this->finder->get($this->modulePath.'/SuperTests/Feature/EloquentPostRepositoryTest.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultFeatureNamespaceSpecific()
    {
        $this->app['config']->set('modules.paths.generator.test-feature.namespace', 'SuperTests\\Feature');

        $code = $this->artisan('module:make-test', ['name' => 'EloquentPostRepositoryTest', 'module' => 'Blog', '--feature' => true]);

        $file = $this->finder->get($this->modulePath.'/Tests/Feature/EloquentPostRepositoryTest.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }
}
