<?php

namespace Nwidart\Modules\Tests;

use Nwidart\Modules\Support\Migrations\NameParser;

class NameParserTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function itGetsTheOriginalName()
    {
        $parser = new NameParser('create_users_table');

        self::assertEquals('create_users_table', $parser->getOriginalName());
    }

    /** @test */
    public function itGetsTheTableName()
    {
        $parser = new NameParser('create_users_table');

        self::assertEquals('users', $parser->getTableName());
    }

    /** @test */
    public function itGetsTheActionName()
    {
        self::assertEquals('create', (new NameParser('create_users_table'))->getAction());
        self::assertEquals('update', (new NameParser('update_users_table'))->getAction());
        self::assertEquals('delete', (new NameParser('delete_users_table'))->getAction());
        self::assertEquals('remove', (new NameParser('remove_users_table'))->getAction());
    }

    /** @test */
    public function itGetsFirstPartOfNameIfNoActionWasGuessed()
    {
        self::assertEquals('something', (new NameParser('something_random'))->getAction());
    }

    /** @test */
    public function itGetsTheCorrectMatchedResults()
    {
        $matches = (new NameParser('create_users_table'))->getMatches();

        $expected = [
            'create_users_table',
            'users',
        ];

        self::assertEquals($expected, $matches);
    }

    /** @test */
    public function itGetsTheExplodedPartsOfMigrationName()
    {
        $parser = new NameParser('create_users_table');

        $expected = [
            'create',
            'users',
            'table',
        ];

        self::assertEquals($expected, $parser->getData());
    }

    /** @test */
    public function itCanCheckIfCurrentMigrationTypeMatchesGivenType()
    {
        $parser = new NameParser('create_users_table');

        self::assertTrue($parser->is('create'));
    }

    /** @test */
    public function itCanCheckIfCurrentMigrationIsAboutAdding()
    {
        self::assertTrue((new NameParser('add_users_table'))->isAdd());
    }

    /** @test */
    public function itCanCheckIfCurrentMigrationIsAboutDeleting()
    {
        self::assertTrue((new NameParser('delete_users_table'))->isDelete());
    }

    /** @test */
    public function itCanCheckIfCurrentMigrationIsAboutCreating()
    {
        self::assertTrue((new NameParser('create_users_table'))->isCreate());
    }

    /** @test */
    public function itCanCheckIfCurrentMigrationIsAboutDropping()
    {
        self::assertTrue((new NameParser('drop_users_table'))->isDrop());
    }

    /** @test */
    public function itMakesARegexPattern()
    {
        self::assertEquals('/create_(.*)_table/', (new NameParser('create_users_table'))->getPattern());
        self::assertEquals('/add_(.*)_to_(.*)_table/', (new NameParser('add_column_to_users_table'))->getPattern());
        self::assertEquals('/delete_(.*)_from_(.*)_table/', (new NameParser('delete_users_table'))->getPattern());
    }
}
