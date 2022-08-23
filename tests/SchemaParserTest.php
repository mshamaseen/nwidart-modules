<?php

namespace Nwidart\Modules\Tests;

use Nwidart\Modules\Support\Migrations\SchemaParser;

class SchemaParserTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function itGeneratesMigrationMethodCalls()
    {
        $parser = new SchemaParser('username:string, password:integer');

        $expected = <<<TEXT
\t\t\t\$table->string('username');
\t\t\t\$table->integer('password');\n
TEXT;

        self::assertEquals($expected, $parser->render());
    }

    /** @test */
    public function itGeneratesMigrationMethodsForUpMethod()
    {
        $parser = new SchemaParser('username:string, password:integer');

        $expected = <<<TEXT
\t\t\t\$table->string('username');
\t\t\t\$table->integer('password');\n
TEXT;

        self::assertEquals($expected, $parser->up());
    }

    /** @test */
    public function itGeneratesMigrationMethodsForDownMethod()
    {
        $parser = new SchemaParser('username:string, password:integer');

        $expected = <<<TEXT
\t\t\t\$table->dropColumn('username');
\t\t\t\$table->dropColumn('password');\n
TEXT;

        self::assertEquals($expected, $parser->down());
    }
}
