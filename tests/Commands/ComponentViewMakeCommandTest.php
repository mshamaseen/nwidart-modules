<?php

namespace Nwidart\Modules\Tests\Commands;

use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class ComponentViewCommandTest extends BaseTestCase
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
    public function itGeneratesTheComponentView()
    {
        $code = $this->artisan('module:make-component-view', ['name' => 'Blog', 'module' => 'Blog']);
        $this->assertTrue(is_file($this->modulePath.'/Resources/views/components/blog.blade.php'));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectFileWithContent()
    {
        $code = $this->artisan('module:make-component-view', ['name' => 'Blog', 'module' => 'Blog']);
        $file = $this->finder->get($this->modulePath.'/Resources/views/components/blog.blade.php');
        $this->assertTrue(str_contains($file, '<div>'));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespace()
    {
        $this->app['config']->set('modules.paths.generator.component-view.path', 'Resources/views/components/newDirectory');

        $code = $this->artisan('module:make-component-view', ['name' => 'Blog', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Resources/views/components/newDirectory/blog.blade.php');

        $this->assertTrue(str_contains($file, '<div>'));
        $this->assertSame(0, $code);
    }
}
