<?php

namespace Nwidart\Modules\Tests\Commands;

use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class PolicyMakeCommandTest extends BaseTestCase
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
    public function itMakesPolicy()
    {
        $code = $this->artisan('module:make-policy', ['name' => 'PostPolicy', 'module' => 'Blog']);

        $policyFile = $this->modulePath.'/Policies/PostPolicy.php';

        $this->assertTrue(is_file($policyFile), 'Policy file was not created.');
        $this->assertMatchesSnapshot($this->finder->get($policyFile));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespace()
    {
        $this->app['config']->set('modules.paths.generator.policies.path', 'SuperPolicies');

        $code = $this->artisan('module:make-policy', ['name' => 'PostPolicy', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/SuperPolicies/PostPolicy.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespaceSpecific()
    {
        $this->app['config']->set('modules.paths.generator.policies.namespace', 'SuperPolicies');

        $code = $this->artisan('module:make-policy', ['name' => 'PostPolicy', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Policies/PostPolicy.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }
}
