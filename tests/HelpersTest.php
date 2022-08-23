<?php

namespace Nwidart\Modules\Tests;

use Illuminate\Support\Str;

class HelpersTest extends BaseTestCase
{
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
        $this->finder->deleteDirectory($this->modulePath);
        parent::tearDown();
    }

    /** @test */
    public function itFindsTheModulePath()
    {
        $this->assertTrue(Str::contains(module_path('Blog'), 'modules/Blog'));
    }

    /** @test */
    public function itCanBindARelativePathToModulePath()
    {
        $this->assertTrue(Str::contains(module_path('Blog', 'config/config.php'), 'modules/Blog/config/config.php'));
    }
}
