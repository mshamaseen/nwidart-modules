<?php

namespace Nwidart\Modules\Tests\Commands;

use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class ComponentClassCommandTest extends BaseTestCase
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
    public function itGeneratesTheComponentClass()
    {
        $code = $this->artisan('module:make-component', ['name' => 'Blog', 'module' => 'Blog']);
        $this->assertTrue(is_file($this->modulePath.'/View/Component/Blog.php'));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesTheComponentViewFromComponentClassCommand()
    {
        $code = $this->artisan('module:make-component', ['name' => 'Blog', 'module' => 'Blog']);
        $file = $this->finder->get($this->modulePath.'/Resources/views/components/blog.blade.php');
        $this->assertTrue(str_contains($file, '<div>'));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectFileWithContent()
    {
        $code = $this->artisan('module:make-component', ['name' => 'Blog', 'module' => 'Blog']);
        $file = $this->finder->get($this->modulePath.'/View/Component/Blog.php');
        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespace()
    {
        $this->app['config']->set('modules.paths.generator.component-class.path', 'View/Component/newDirectory');

        $code = $this->artisan('module:make-component', ['name' => 'Blog', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/View/Component/newDirectory/Blog.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }
}
