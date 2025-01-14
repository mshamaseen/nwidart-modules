<?php

namespace Nwidart\Modules\Tests\Commands;

use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class RequestMakeCommandTest extends BaseTestCase
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
    public function itGeneratesANewFormRequestClass()
    {
        $code = $this->artisan('module:make-request', ['name' => 'CreateBlogPostRequest', 'module' => 'Blog']);

        $this->assertTrue(is_file($this->modulePath.'/Http/Requests/CreateBlogPostRequest.php'));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectFileWithContent()
    {
        $code = $this->artisan('module:make-request', ['name' => 'CreateBlogPostRequest', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Http/Requests/CreateBlogPostRequest.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespace()
    {
        $this->app['config']->set('modules.paths.generator.request.path', 'SuperRequests');

        $code = $this->artisan('module:make-request', ['name' => 'CreateBlogPostRequest', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/SuperRequests/CreateBlogPostRequest.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespaceSpecific()
    {
        $this->app['config']->set('modules.paths.generator.request.namespace', 'SuperRequests');

        $code = $this->artisan('module:make-request', ['name' => 'CreateBlogPostRequest', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Http/Requests/CreateBlogPostRequest.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }
}
