<?php

namespace Zork\Model\Mapper\DbAware;

use Zork\Test\PHPUnit\DbAdapterAware\TestCase;

/**
 * ReadWriteMapperAbstractTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ReadWriteMapperAbstractTest extends TestCase
{

    /**
     * @var array
     */
    protected static $sampleData = array(
        'int'       => '3',
        'bool'      => 'f',
        'float'     => '3.14',
        'string'    => null,
        'text'      => 'text',
        'date'      => '2014-01-06',
        'datetime'  => '2014-01-06T15:51:15+0000',
        'numbers'   => '{1, 2, NULL, 3.14}',
    );

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        include_once __DIR__ . '/_files/MapperAbstractTestClasses.php';
    }

    /**
     * Test deselect()
     */
    public function testDeselected()
    {
        $mapper     = new TestReadWriteMapper;
        $structure  = $mapper->selected( static::$sampleData );
        $data       = $mapper->deselect( $structure );

        $this->assertEquals( static::$sampleData, $data );
    }

    /**
     * Test create()
     */
    public function testCreate()
    {
        $mapper     = new TestReadWriteMapper;
        $structure  = $mapper->create( array(
            'int'       => 3,
            'bool'      => false,
            'float'     => 3.14,
        ) );

        $this->assertInstanceOf(
            'Zork\Model\Mapper\DbAware\TestReadWriteStructure',
            $structure
        );

        $this->assertSame( 3, $structure->int );
        $this->assertSame( false, $structure->bool );
        $this->assertSame( 3.14, $structure->float );
    }

    /**
     * Test insert() in save()
     */
    public function testSaveInsert()
    {
        /* @var $mapper TestReadWriteMapper */
        $mapper     = $this->getMockedDbAdapterAware( new TestReadWriteMapper );
        $structure  = $mapper->selected( static::$sampleData );
        $data       = static::$sampleData;

        $structure->int = null;
        unset( $data['string'] );
        $data = array_values( $data );
        $int  = array_shift( $data );

        $this->expectStatementSql(
            'INSERT INTO "table_name" ' .
            '("bool", "float", "string", ' .
            '"text", "date", "datetime", "numbers") ' .
            'VALUES (?, ?, DEFAULT, ?, ?, ?, ?)',
            $data, 1
        );

        $this->getMockedDbConnection()
             ->expects( $this->once() )
             ->method( 'getLastGeneratedValue' )
             ->with( '"int_seq"' )
             ->will( $this->returnValue( $int ) );

        $result = $mapper->save( $structure );
        $this->assertSame( 1, $result );
        $this->assertEquals( $int, $structure->int );
    }

    /**
     * Test update() in save()
     */
    public function testSaveUpdate()
    {
        /* @var $mapper TestReadWriteMapper */
        $mapper     = $this->getMockedDbAdapterAware( new TestReadWriteMapper );
        $structure  = $mapper->selected( static::$sampleData );
        $data       = static::$sampleData;

        unset( $data['string'] );
        $data = array_values( $data );
        $data[] = array_shift( $data );

        $this->expectStatementSql(
            'UPDATE "table_name" SET ' .
            '"bool" = ?, "float" = ?, "string" = DEFAULT, ' .
            '"text" = ?, "date" = ?, "datetime" = ?, "numbers" = ? ' .
            'WHERE "int" = ?',
            $data, 1
        );

        $result = $mapper->save( $structure );
        $this->assertSame( 1, $result );
    }

    /**
     * Test delete() with a structure
     */
    public function testDeleteWithStructure()
    {
        /* @var $mapper TestReadWriteMapper */
        $mapper     = $this->getMockedDbAdapterAware( new TestReadWriteMapper );
        $structure  = $mapper->selected( static::$sampleData );

        $this->expectStatementSql(
            'DELETE FROM "table_name" WHERE "int" = ?',
            array( static::$sampleData['int'] ), 1
        );

        $result = $mapper->delete( $structure );
        $this->assertSame( 1, $result );
    }

    /**
     * Test delete() with a hash table
     */
    public function testDeleteWithHashTable()
    {
        /* @var $mapper TestReadWriteMapper */
        $mapper = $this->getMockedDbAdapterAware( new TestReadWriteMapper );

        $this->expectStatementSql(
            'DELETE FROM "table_name" WHERE "int" = ?',
            array( 3 ), 1
        );

        $result = $mapper->delete( array( 'int' => 3 ) );
        $this->assertSame( 1, $result );
    }

    /**
     * Test delete() with an array
     */
    public function testDeleteWithArray()
    {
        /* @var $mapper TestReadWriteMapper */
        $mapper = $this->getMockedDbAdapterAware( new TestReadWriteMapper );

        $this->expectStatementSql(
            'DELETE FROM "table_name" WHERE "int" = ?',
            array( 3 ), 1
        );

        $result = $mapper->delete( array( 3 ) );
        $this->assertSame( 1, $result );
    }

    /**
     * Test delete() with explicit primary keys
     */
    public function testDeleteWithPrimaryKeys()
    {
        /* @var $mapper TestReadWriteMapper */
        $mapper = $this->getMockedDbAdapterAware( new TestReadWriteMapper );

        $this->expectStatementSql(
            'DELETE FROM "table_name" WHERE "int" = ?',
            array( 3 ), 1
        );

        $result = $mapper->delete( 3 );
        $this->assertSame( 1, $result );
    }

}
