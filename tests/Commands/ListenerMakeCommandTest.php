<?php

namespace Nwidart\Modules\Tests\Commands;

use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class ListenerMakeCommandTest extends BaseTestCase
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
        $code = $this->artisan(
            'module:make-listener',
            ['name' => 'NotifyUsersOfANewPost', 'module' => 'Blog', '--event' => 'UserWasCreated']
        );

        $this->assertTrue(is_file($this->modulePath.'/Listeners/NotifyUsersOfANewPost.php'));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectSyncEventWithContent()
    {
        $code = $this->artisan(
            'module:make-listener',
            ['name' => 'NotifyUsersOfANewPost', 'module' => 'Blog', '--event' => 'UserWasCreated']
        );

        $file = $this->finder->get($this->modulePath.'/Listeners/NotifyUsersOfANewPost.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectSyncEventInASubdirectoryWithContent()
    {
        $code = $this->artisan(
            'module:make-listener',
            ['name' => 'NotifyUsersOfANewPost', 'module' => 'Blog', '--event' => 'User/WasCreated']
        );

        $file = $this->finder->get($this->modulePath.'/Listeners/NotifyUsersOfANewPost.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectSyncDuckEventWithContent()
    {
        $code = $this->artisan(
            'module:make-listener',
            ['name' => 'NotifyUsersOfANewPost', 'module' => 'Blog']
        );

        $file = $this->finder->get($this->modulePath.'/Listeners/NotifyUsersOfANewPost.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectQueuedEventWithContent()
    {
        $code = $this->artisan(
            'module:make-listener',
            ['name' => 'NotifyUsersOfANewPost', 'module' => 'Blog', '--event' => 'UserWasCreated', '--queued' => true]
        );

        $file = $this->finder->get($this->modulePath.'/Listeners/NotifyUsersOfANewPost.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectQueuedEventInASubdirectoryWithContent()
    {
        $code = $this->artisan(
            'module:make-listener',
            ['name' => 'NotifyUsersOfANewPost', 'module' => 'Blog', '--event' => 'User/WasCreated', '--queued' => true]
        );

        $file = $this->finder->get($this->modulePath.'/Listeners/NotifyUsersOfANewPost.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectQueuedDuckEventWithContent()
    {
        $code = $this->artisan(
            'module:make-listener',
            ['name' => 'NotifyUsersOfANewPost', 'module' => 'Blog', '--queued' => true]
        );

        $file = $this->finder->get($this->modulePath.'/Listeners/NotifyUsersOfANewPost.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespace()
    {
        $this->app['config']->set('modules.paths.generator.listener.path', 'Events/Handlers');

        $code = $this->artisan(
            'module:make-listener',
            ['name' => 'NotifyUsersOfANewPost', 'module' => 'Blog']
        );

        $file = $this->finder->get($this->modulePath.'/Events/Handlers/NotifyUsersOfANewPost.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespaceSpecific()
    {
        $this->app['config']->set('modules.paths.generator.listener.namespace', 'Events\\Handlers');

        $code = $this->artisan(
            'module:make-listener',
            ['name' => 'NotifyUsersOfANewPost', 'module' => 'Blog']
        );

        $file = $this->finder->get($this->modulePath.'/Listeners/NotifyUsersOfANewPost.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }
}
