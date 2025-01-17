<?php

namespace Nwidart\Modules\Tests\Commands;

use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class EventMakeCommandTest extends BaseTestCase
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
    public function itGeneratesANewEventClass()
    {
        $code = $this->artisan('module:make-event', ['name' => 'PostWasCreated', 'module' => 'Blog']);

        $this->assertTrue(is_file($this->modulePath.'/Events/PostWasCreated.php'));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectFileWithContent()
    {
        $code = $this->artisan('module:make-event', ['name' => 'PostWasCreated', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Events/PostWasCreated.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespace()
    {
        $this->app['config']->set('modules.paths.generator.event.path', 'SuperEvents');

        $code = $this->artisan('module:make-event', ['name' => 'PostWasCreated', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/SuperEvents/PostWasCreated.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespaceSpecific()
    {
        $this->app['config']->set('modules.paths.generator.event.namespace', 'SuperEvents');

        $code = $this->artisan('module:make-event', ['name' => 'PostWasCreated', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Events/PostWasCreated.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }
}
