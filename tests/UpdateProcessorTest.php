<?php

use Vimeo\MysqlEngine\Schema\TableDefinition;
use Vimeo\MysqlEngine\Server;

class UpdateProcessorTest extends \PHPUnit\Framework\TestCase
{
    public function testBinaryExpressionWithVariableExpressionCanBePartOfBinaryExpression()
    {
        $tableDefinition = new TableDefinition(
            'foo',
            'foo',
            [
                'id' => new \Vimeo\MysqlEngine\Schema\Column\IntColumn(true,5),
                'bar' => new \Vimeo\MysqlEngine\Schema\Column\Char(255)
            ]
        );
        $server = Server::getOrCreate('primary');
        $server->addTableDefinition('foo', 'foo', $tableDefinition);
        $conn = self::getPdo('mysql:dbname=foo;');
        $this->assertEquals('foo', $conn->getDatabaseName());

        $query = 'UPDATE `foo` SET `bar` = @uservariablefoo := \'barvalue\' WHERE id = 1';

        $update_query = \Vimeo\MysqlEngine\Parser\SQLParser::parse($query);

        $conn->getServer()->saveTable('foo', 'foo', [ 1 => ['id' => 1, 'bar' => 'oldvalue']]);

        \Vimeo\MysqlEngine\Processor\UpdateProcessor::process(
            $conn,
            new \Vimeo\MysqlEngine\Processor\Scope([], $conn->getVariables()),
            $update_query
        );

        $rows = $conn->getServer()->getTable('foo', 'foo');
        $this->assertSame( [ 1 => ['id' => 1, 'bar' => 'barvalue']], $rows);

        $this->assertSame(['uservariablefoo' => 'barvalue'] , $conn->getVariables());
    }

    private static function getPdo(string $connection_string): \PDO
    {
        if (\PHP_MAJOR_VERSION === 8) {
            return new \Vimeo\MysqlEngine\Php8\FakePdo($connection_string);
        }

        return new \Vimeo\MysqlEngine\Php7\FakePdo($connection_string);
    }
}
