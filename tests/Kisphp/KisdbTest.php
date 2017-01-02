<?php

namespace Test\Kisphp;

use Kisphp\Kisdb;

// http://code.tutsplus.com/tutorials/php-database-access-are-you-doing-it-correctly--net-25338

class KisdbTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $db = Kisdb::getInstance();
        $db->connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
        $db->enableDebug();

        $this->importSchema();
    }

    public function tearDown()
    {
        $db = Kisdb::getInstance();
        $db->query("DROP TABLE test_table");
    }

    public function test_insert()
    {
        $db = Kisdb::getInstance();

        $db->insert('test_table', [
            'column_1' => 'value_1',
            'column_2' => 'value_2',
        ]);

        $this->assertSame(
            'INSERT INTO test_table SET column_1 = \'value_1\', column_2 = \'value_2\'',
            $db->getLog()->getLastQuery()
        );
    }

    public function test_insert_ignore()
    {
        $db = Kisdb::getInstance();

        $db->insert(
            'test_table',
            [
                'column_1' => 'value_1',
                'column_2' => 'value_2',
            ],
            true
        );

        $this->assertSame(
            'INSERT IGNORE INTO test_table SET column_1 = \'value_1\', column_2 = \'value_2\'',
            $db->getLog()->getLastQuery()
        );
    }

    public function test_update_simple_value()
    {
        $db = Kisdb::getInstance();

        $db->update('test_table', [
            'column_1' => 'value_1',
            'column_2' => 'value_2',
        ], 20);

        $this->assertSame(
            'UPDATE test_table SET column_1 = \'value_1\', column_2 = \'value_2\' WHERE id = \'20\'',
            $db->getLog()->getLastQuery()
        );
    }

    public function test_update_simple_value_on_other_column()
    {
        $db = Kisdb::getInstance();

        $db->update('test_table', [
            'column_1' => 'value_1',
            'column_2' => 'value_2',
        ], 'my title', 'title');

        $this->assertSame(
            'UPDATE test_table SET column_1 = \'value_1\', column_2 = \'value_2\' WHERE title = \'my title\'',
            $db->getLog()->getLastQuery()
        );
    }

    public function test_get_value()
    {
        $db = Kisdb::getInstance();

        $value = $db->getValue("SELECT column_1 FROM test_table");

        $this->assertSame('c1.1', $value);
    }

    public function test_get_pairs()
    {
        $db = Kisdb::getInstance();

        $pairs = $db->getPairs("SELECT id, column_1 FROM test_table");

        $this->assertSame([
            '1' => 'c1.1',
            '2' => 'c2.1',
            '3' => 'c3.1',
        ], $pairs);
    }

    public function test_select()
    {
        $db = Kisdb::getInstance();

        $a = $db->query("SELECT * FROM test_table ");

        while ($b = $a->fetch(\PDO::FETCH_ASSOC)) {
            $this->assertGreaterThan(2, count($b));
        }
    }

    private function importSchema()
    {
        $db = Kisdb::getInstance();

        $sqlFile = dirname(__DIR__) . '/fixtures/import.sql';
        $queries = explode(';', file_get_contents($sqlFile));

        foreach ($queries as $query) {
            $sql = trim($query);

            $db->query($sql);
        }
    }
}
