<?php

namespace Nwidart\Modules\Tests;

use Nwidart\Modules\Facades\Module;

class ModuleFacadeTest extends BaseTestCase
{
    /** @test */
    public function itResolvesTheModuleFacade()
    {
        $modules = Module::all();

        $this->assertTrue(is_array($modules));
    }

    /** @test */
    public function itCreatesMacrosViaFacade()
    {
        $modules = Module::macro('testMacro', function () {
            return true;
        });

        $this->assertTrue(Module::hasMacro('testMacro'));
    }

    /** @test */
    public function itCallsMacrosViaFacade()
    {
        $modules = Module::macro('testMacro', function () {
            return 'a value';
        });

        $this->assertEquals('a value', Module::testMacro());
    }
}
