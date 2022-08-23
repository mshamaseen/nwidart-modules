<?php

namespace Nwidart\Modules\Tests;

use Illuminate\Support\Str;
use Nwidart\Modules\Support\Stub;

class StubTest extends BaseTestCase
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $finder;

    public function setUp(): void
    {
        parent::setUp();
        $this->finder = $this->app['files'];
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->finder->delete([
            base_path('my-command.php'),
            base_path('stub-override-exists.php'),
            base_path('stub-override-not-exists.php'),
        ]);
    }

    /** @test */
    public function itInitialisesAStubInstance()
    {
        $stub = new Stub('/model.stub', [
            'NAME' => 'Name',
        ]);

        $this->assertTrue(Str::contains($stub->getPath(), 'src/Commands/stubs/model.stub'));
        $this->assertEquals(['NAME' => 'Name'], $stub->getReplaces());
    }

    /** @test */
    public function itSetsNewReplacesArray()
    {
        $stub = new Stub('/model.stub', [
            'NAME' => 'Name',
        ]);

        $stub->replace(['VENDOR' => 'MyVendor']);
        $this->assertEquals(['VENDOR' => 'MyVendor'], $stub->getReplaces());
    }

    /** @test */
    public function itStoresStubToSpecificPath()
    {
        $stub = new Stub('/command.stub', [
            'COMMAND_NAME' => 'my:command',
            'NAMESPACE' => 'Blog\Commands',
            'CLASS' => 'MyCommand',
        ]);

        $stub->saveTo(base_path(), 'my-command.php');

        $this->assertTrue($this->finder->exists(base_path('my-command.php')));
    }

    /** @test */
    public function itSetsNewPath()
    {
        $stub = new Stub('/model.stub', [
            'NAME' => 'Name',
        ]);

        $stub->setPath('/new-path/');

        $this->assertTrue(Str::contains($stub->getPath(), 'Commands/stubs/new-path/'));
    }

    /** @test */
    public function useDefaultStubIfOverrideNotExists()
    {
        $stub = new Stub('/command.stub', [
            'COMMAND_NAME' => 'my:command',
            'NAMESPACE' => 'Blog\Commands',
            'CLASS' => 'MyCommand',
        ]);

        $stub->setBasePath(__DIR__.'/stubs');

        $stub->saveTo(base_path(), 'stub-override-not-exists.php');

        $this->assertTrue($this->finder->exists(base_path('stub-override-not-exists.php')));
    }

    /** @test */
    public function useOverrideStubIfExists()
    {
        $stub = new Stub('/model.stub', [
            'NAME' => 'Name',
        ]);

        $stub->setBasePath(__DIR__.'/stubs');

        $stub->saveTo(base_path(), 'stub-override-exists.php');

        $this->assertTrue($this->finder->exists(base_path('stub-override-exists.php')));
        $this->assertEquals('stub-override', $this->finder->get(base_path('stub-override-exists.php')));
    }
}
