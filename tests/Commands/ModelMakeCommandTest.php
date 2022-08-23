<?php

namespace Nwidart\Modules\Tests\Commands;

use Illuminate\Support\Facades\Artisan;
use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class ModelMakeCommandTest extends BaseTestCase
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
    public function itGeneratesANewModelClass()
    {
        $code = $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog']);

        $this->assertTrue(is_file($this->modulePath.'/Entities/Post.php'));
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratedCorrectFileWithContent()
    {
        $code = $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Entities/Post.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesCorrectFillableFields()
    {
        $code = $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog', '--fillable' => 'title,slug']);

        $file = $this->finder->get($this->modulePath.'/Entities/Post.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesMigrationFileWithModel()
    {
        $code = $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog', '--migration' => true]);

        $migrations = $this->finder->allFiles($this->modulePath.'/Database/Migrations');
        $migrationFile = $migrations[0];
        $migrationContent = $this->finder->get($this->modulePath.'/Database/Migrations/'.$migrationFile->getFilename());
        $this->assertCount(1, $migrations);
        $this->assertMatchesSnapshot($migrationContent);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesMigrationFileWithModelUsingShortcutOption()
    {
        $code = $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog', '-m' => true]);

        $migrations = $this->finder->allFiles($this->modulePath.'/Database/Migrations');
        $migrationFile = $migrations[0];
        $migrationContent = $this->finder->get($this->modulePath.'/Database/Migrations/'.$migrationFile->getFilename());
        $this->assertCount(1, $migrations);
        $this->assertMatchesSnapshot($migrationContent);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesControllerFileWithModel()
    {
        $code = $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog', '--controller' => true]);
        $controllers = $this->finder->allFiles($this->modulePath.'/Http/Controllers');
        $controllerFile = $controllers[1];
        $controllerContent = $this->finder->get($this->modulePath.'/Http/Controllers/'.$controllerFile->getFilename());
        $this->assertCount(2, $controllers);
        $this->assertMatchesSnapshot($controllerContent);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesControllerFileWithModelUsingShortcutOption()
    {
        $code = $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog', '-c' => true]);

        $controllers = $this->finder->allFiles($this->modulePath.'/Http/Controllers');
        $controllerFile = $controllers[1];
        $controllerContent = $this->finder->get($this->modulePath.'/Http/Controllers/'.$controllerFile->getFilename());
        $this->assertCount(2, $controllers);
        $this->assertMatchesSnapshot($controllerContent);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesControllerAndMigrationWhenBothFlagsArePresent()
    {
        $code = $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog', '-c' => true, '-m' => true]);

        $controllers = $this->finder->allFiles($this->modulePath.'/Http/Controllers');
        $controllerFile = $controllers[1];
        $controllerContent = $this->finder->get($this->modulePath.'/Http/Controllers/'.$controllerFile->getFilename());
        $this->assertCount(2, $controllers);
        $this->assertMatchesSnapshot($controllerContent);

        $migrations = $this->finder->allFiles($this->modulePath.'/Database/Migrations');
        $migrationFile = $migrations[0];
        $migrationContent = $this->finder->get($this->modulePath.'/Database/Migrations/'.$migrationFile->getFilename());
        $this->assertCount(1, $migrations);
        $this->assertMatchesSnapshot($migrationContent);

        $this->assertSame(0, $code);
    }

    /** @test */
    public function itGeneratesCorrectMigrationFileNameWithMultipleWordsModel()
    {
        $code = $this->artisan('module:make-model', ['model' => 'ProductDetail', 'module' => 'Blog', '-m' => true]);

        $migrations = $this->finder->allFiles($this->modulePath.'/Database/Migrations');
        $migrationFile = $migrations[0];
        $migrationContent = $this->finder->get($this->modulePath.'/Database/Migrations/'.$migrationFile->getFilename());

        $this->assertStringContainsString('create_product_details_table', $migrationFile->getFilename());
        $this->assertMatchesSnapshot($migrationContent);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itDisplaysErrorIfModelAlreadyExists()
    {
        $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog']);
        $code = $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog']);

        $this->assertStringContainsString('already exists', Artisan::output());
        $this->assertSame(E_ERROR, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespace()
    {
        $this->app['config']->set('modules.paths.generator.model.path', 'Models');

        $code = $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Models/Post.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    /** @test */
    public function itCanChangeTheDefaultNamespaceSpecific()
    {
        $this->app['config']->set('modules.paths.generator.model.namespace', 'Models');

        $code = $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath.'/Entities/Post.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }
}
