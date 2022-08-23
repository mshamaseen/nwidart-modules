<?php

namespace Nwidart\Modules\Tests;

use Illuminate\Support\Facades\Event;
use Nwidart\Modules\Contracts\ActivatorInterface;
use Nwidart\Modules\Json;

class LumenModuleTest extends BaseTestCase
{
    /**
     * @var LumenTestingModule
     */
    private $module;

    /**
     * @var ActivatorInterface
     */
    private $activator;

    public function setUp(): void
    {
        parent::setUp();
        $this->module = new LumenTestingModule($this->app, 'Recipe Name', __DIR__.'/stubs/valid/Recipe');
        $this->activator = $this->app[ActivatorInterface::class];
    }

    public function tearDown(): void
    {
        $this->activator->reset();
        parent::tearDown();
    }

    /** @test */
    public function itGetsModuleName()
    {
        $this->assertEquals('Recipe Name', $this->module->getName());
    }

    /** @test */
    public function itGetsLowercaseModuleName()
    {
        $this->assertEquals('recipe name', $this->module->getLowerName());
    }

    /** @test */
    public function itGetsStudlyName()
    {
        $this->assertEquals('RecipeName', $this->module->getStudlyName());
    }

    /** @test */
    public function itGetsSnakeName()
    {
        $this->assertEquals('recipe_name', $this->module->getSnakeName());
    }

    /** @test */
    public function itGetsModuleDescription()
    {
        $this->assertEquals('recipe module', $this->module->getDescription());
    }

    /** @test */
    public function itGetsModuleAlias()
    {
        $this->assertEquals('recipe', $this->module->getAlias());
    }

    /** @test */
    public function itGetsModulePath()
    {
        $this->assertEquals(__DIR__.'/stubs/valid/Recipe', $this->module->getPath());
    }

    /** @test */
    public function itGetsRequiredModules()
    {
        $this->assertEquals(['required_module'], $this->module->getRequires());
    }

    /** @test */
    public function itLoadsModuleTranslations()
    {
        (new LumenTestingModule($this->app, 'Recipe', __DIR__.'/stubs/valid/Recipe'))->boot();
        $this->assertEquals('Recipe', trans('recipe::recipes.title.recipes'));
    }

    /** @test */
    public function itReadsModuleJsonFiles()
    {
        $jsonModule = $this->module->json();
        $composerJson = $this->module->json('composer.json');

        $this->assertInstanceOf(Json::class, $jsonModule);
        $this->assertEquals('0.1', $jsonModule->get('version'));
        $this->assertInstanceOf(Json::class, $composerJson);
        $this->assertEquals('asgard-module', $composerJson->get('type'));
    }

    /** @test */
    public function itReadsKeyFromModuleJsonFileViaHelperMethod()
    {
        $this->assertEquals('Recipe', $this->module->get('name'));
        $this->assertEquals('0.1', $this->module->get('version'));
        $this->assertEquals('my default', $this->module->get('some-thing-non-there', 'my default'));
        $this->assertEquals(['required_module'], $this->module->get('requires'));
    }

    /** @test */
    public function itReadsKeyFromComposerJsonFileViaHelperMethod()
    {
        $this->assertEquals('nwidart/recipe', $this->module->getComposerAttr('name'));
    }

    /** @test */
    public function itCastsModuleToString()
    {
        $this->assertEquals('RecipeName', (string) $this->module);
    }

    /** @test */
    public function itModuleStatusCheck()
    {
        $this->assertFalse($this->module->isStatus(true));
        $this->assertTrue($this->module->isStatus(false));
    }

    /** @test */
    public function itChecksModuleEnabledStatus()
    {
        $this->assertFalse($this->module->isEnabled());
        $this->assertTrue($this->module->isDisabled());
    }

    /** @test */
    public function itFiresEventsWhenModuleIsEnabled()
    {
        Event::fake();

        $this->module->enable();

        Event::assertDispatched(sprintf('modules.%s.enabling', $this->module->getLowerName()));
        Event::assertDispatched(sprintf('modules.%s.enabled', $this->module->getLowerName()));
    }

    /** @test */
    public function itFiresEventsWhenModuleIsDisabled()
    {
        Event::fake();

        $this->module->disable();

        Event::assertDispatched(sprintf('modules.%s.disabling', $this->module->getLowerName()));
        Event::assertDispatched(sprintf('modules.%s.disabled', $this->module->getLowerName()));
    }

    /** @test */
    public function itHasAGoodProvidersManifestPath()
    {
        $this->assertEquals(
            $this->app->basePath("storage/app/{$this->module->getSnakeName()}_module.php"),
            $this->module->getCachedServicesPath()
        );
    }
}

class LumenTestingModule extends \Nwidart\Modules\Lumen\Module
{
}
