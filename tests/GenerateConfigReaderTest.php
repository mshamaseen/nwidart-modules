<?php

namespace Nwidart\Modules\Tests;

use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Config\GeneratorPath;

final class GenerateConfigReaderTest extends BaseTestCase
{
    /** @test */
    public function itCanReadAConfigurationValueWithNewFormat()
    {
        $seedConfig = GenerateConfigReader::read('seeder');

        $this->assertInstanceOf(GeneratorPath::class, $seedConfig);
        $this->assertEquals('Database/Seeders', $seedConfig->getPath());
        $this->assertTrue($seedConfig->generate());
    }

    /** @test */
    public function itCanReadAConfigurationValueWithNewFormatSetToFalse()
    {
        $this->app['config']->set('modules.paths.generator.seeder', ['path' => 'Database/Seeders', 'generate' => false]);

        $seedConfig = GenerateConfigReader::read('seeder');

        $this->assertInstanceOf(GeneratorPath::class, $seedConfig);
        $this->assertEquals('Database/Seeders', $seedConfig->getPath());
        $this->assertFalse($seedConfig->generate());
    }

    /** @test */
    public function itCanReadAConfigurationValueWithOldFormat()
    {
        $this->app['config']->set('modules.paths.generator.seeder', 'Database/Seeders');

        $seedConfig = GenerateConfigReader::read('seeder');

        $this->assertInstanceOf(GeneratorPath::class, $seedConfig);
        $this->assertEquals('Database/Seeders', $seedConfig->getPath());
        $this->assertTrue($seedConfig->generate());
    }

    /** @test */
    public function itCanReadAConfigurationValueWithOldFormatSetToFalse()
    {
        $this->app['config']->set('modules.paths.generator.seeder', false);

        $seedConfig = GenerateConfigReader::read('seeder');

        $this->assertInstanceOf(GeneratorPath::class, $seedConfig);
        $this->assertFalse($seedConfig->getPath());
        $this->assertFalse($seedConfig->generate());
    }

    /** @test */
    public function itCanGuessNamespaceFromPath()
    {
        $this->app['config']->set('modules.paths.generator.provider', ['path' => 'Base/Providers', 'generate' => true]);

        $config = GenerateConfigReader::read('provider');

        $this->assertEquals('Base/Providers', $config->getPath());
        $this->assertEquals('Base\Providers', $config->getNamespace());
    }
}
