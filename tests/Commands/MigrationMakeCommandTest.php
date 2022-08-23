<?php

namespace Nwidart\Modules\Tests\Commands;

use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class MigrationMakeCommandTest extends BaseTestCase
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
    public function itGeneratesANewMigrationClass()
    {
        $code = $this->artisan('module:make-migration', ['name' => 'create_posts_table', 'module' => 'Blog']);

        $files = $this->finder->allFiles($this->modulePath.'/Database/Migrations');

        $this->assertCount(1, $files);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesCorrectCreateMigrationFileContent()
    {
        $code = $this->artisan('module:make-migration', ['name' => 'create_posts_table', 'module' => 'Blog']);

        $migrations = $this->finder->allFiles($this->modulePath.'/Database/Migrations');
        $fileName = $migrations[0]->getRelativePathname();
        $file = $this->finder->get($this->modulePath.'/Database/Migrations/'.$fileName);

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesCorrectAddMigrationFileContent()
    {
        $code = $this->artisan('module:make-migration', ['name' => 'add_something_to_posts_table', 'module' => 'Blog']);

        $migrations = $this->finder->allFiles($this->modulePath.'/Database/Migrations');
        $fileName = $migrations[0]->getRelativePathname();
        $file = $this->finder->get($this->modulePath.'/Database/Migrations/'.$fileName);

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesCorrectDeleteMigrationFileContent()
    {
        $code = $this->artisan('module:make-migration', ['name' => 'delete_something_from_posts_table', 'module' => 'Blog']);

        $migrations = $this->finder->allFiles($this->modulePath.'/Database/Migrations');
        $fileName = $migrations[0]->getRelativePathname();
        $file = $this->finder->get($this->modulePath.'/Database/Migrations/'.$fileName);

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesCorrectDropMigrationFileContent()
    {
        $code = $this->artisan('module:make-migration', ['name' => 'drop_posts_table', 'module' => 'Blog']);

        $migrations = $this->finder->allFiles($this->modulePath.'/Database/Migrations');
        $fileName = $migrations[0]->getRelativePathname();
        $file = $this->finder->get($this->modulePath.'/Database/Migrations/'.$fileName);

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesCorrectDefaultMigrationFileContent()
    {
        $code = $this->artisan('module:make-migration', ['name' => 'something_random_name', 'module' => 'Blog']);

        $migrations = $this->finder->allFiles($this->modulePath.'/Database/Migrations');
        $fileName = $migrations[0]->getRelativePathname();
        $file = $this->finder->get($this->modulePath.'/Database/Migrations/'.$fileName);

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesForeignKeyConstraints()
    {
        $code = $this->artisan('module:make-migration', ['name' => 'create_posts_table', 'module' => 'Blog', '--fields' => 'belongsTo:user:id:users']);

        $migrations = $this->finder->allFiles($this->modulePath.'/Database/Migrations');
        $fileName = $migrations[0]->getRelativePathname();
        $file = $this->finder->get($this->modulePath.'/Database/Migrations/'.$fileName);

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }
}
