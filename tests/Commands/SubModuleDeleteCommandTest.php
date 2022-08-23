<?php

namespace Nwidart\Modules\Commands;

use Nwidart\Modules\Activators\FileActivator;
use Nwidart\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class SubModuleDeleteCommandTest extends BaseTestCase
{
    use MatchesSnapshots;

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
        $this->finder = $this->app['files'];
        $this->activator = new FileActivator($this->app);
    }

    /** @test */
    public function itCanDeleteAModuleFromDisk(): void
    {
        $this->artisan('module:make', ['name' => ['Blog/WrongSubModule']]);
        $this->assertDirectoryExists(base_path('modules/Blog/WrongSubModule'));

        $code = $this->artisan('module:delete', ['module' => 'Blog/WrongSubModule']);
        $this->assertFileDoesNotExist(base_path('modules/Blog/WrongSubModule'));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itDeletesModulesFromStatusFile(): void
    {
        $this->artisan('module:make', ['name' => ['Blog/WrongSubModule']]);
        $this->assertMatchesSnapshot($this->finder->get($this->activator->getStatusesFilePath()));

        $code = $this->artisan('module:delete', ['module' => 'Blog/WrongSubModule']);
        $this->assertMatchesSnapshot($this->finder->get($this->activator->getStatusesFilePath()));
        $this->assertSame(0, $code);
    }
}
