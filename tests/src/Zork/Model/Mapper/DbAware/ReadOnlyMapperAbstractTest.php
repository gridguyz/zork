<?php

namespace Zork\Model\Mapper\DbAware;

use Zork\Test\PHPUnit\DbAdapterAware\TestCase;

/**
 * ReadOnlyMapperAbstractTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ReadOnlyMapperAbstractTest extends TestCase
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
     * Test structure prototype
     */
    public function testStructurePrototype()
    {
        $mapper = new TestReadOnlyMapper;

        $this->assertInstanceOf(
            'Zork\Model\Mapper\DbAware\TestReadOnlyStructure',
            $mapper->getStructurePrototype()
        );

        $prototype = new TestReadOnlyStructure;
        $mapper->setStructurePrototype( $prototype );
        $this->assertSame( $prototype, $mapper->getStructurePrototype() );
    }

    /**
     * Test select()
     */
    public function testSelect()
    {
        /* @var $mapper TestReadOnlyMapper */
        /* @var $select \Zend\Db\Sql\Select */
        $mapper = $this->getMockedDbAdapterAware( new TestReadOnlyMapper );
        $select = $mapper->select( null, false );
        $this->assertInstanceOf( 'Zend\Db\Sql\Select', $select );
        $this->assertEquals(
            'SELECT "int" AS "int", ' .
            '"bool" AS "bool", ' .
            '"float" AS "float", ' .
            '"string" AS "string", ' .
            '"text" AS "text", ' .
            '"date" AS "date", ' .
            '"datetime" AS "datetime", ' .
            '"numbers" AS "numbers" ' .
            'FROM "table_name"',
            $select->getSqlString()
        );

        $mapper->setDbSchema( 'schema_name' );
        $this->assertEquals( 'schema_name', $mapper->getDbSchema() );

        $select = $mapper->select( array( 'int', 'bool' ) );
        $this->assertInstanceOf( 'Zend\Db\Sql\Select', $select );
        $this->assertEquals(
            'SELECT "schema_name"."table_name"."int" AS "int", ' .
            '"schema_name"."table_name"."bool" AS "bool" ' .
            'FROM "schema_name"."table_name"',
            $select->getSqlString()
        );
    }

    /**
     * Test selected()
     */
    public function testSelected()
    {
        /* @var $structure TestReadOnlyStructure */
        $mapper     = new TestReadOnlyMapper;
        $structure  = $mapper->selected( static::$sampleData );

        $this->assertInstanceOf(
            'Zork\Model\Mapper\DbAware\TestReadOnlyStructure',
            $structure
        );

        $this->assertSame( 3, $structure->int );
        $this->assertSame( false, $structure->bool );
        $this->assertSame( 3.14, $structure->float );
        $this->assertNull( $structure->string );
        $this->assertSame( 'text', $structure->text );
        $this->assertInstanceOf( 'DateTime', $structure->date );
        $this->assertEquals( array( 1, 2, null, 3.14 ), $structure->numbers );
        $this->assertSame( '2014-01-06', $structure->date->format( 'Y-m-d' ) );
        $this->assertInstanceOf( 'DateTime', $structure->datetime );
        $this->assertSame( '2014-01-06T15:51:15+0000',
                           $structure->datetime->format( \DateTime::ISO8601 ) );
    }

    /**
     * Test find()
     */
    public function testFind()
    {
        /* @var $mapper TestReadOnlyMapper */
        $mapper = $this->getMockedDbAdapterAware( new TestReadOnlyMapper );

        $this->expectStatementSql(
            'SELECT "table_name"."int" AS "int", ' .
            '"table_name"."bool" AS "bool", ' .
            '"table_name"."float" AS "float", ' .
            '"table_name"."string" AS "string", ' .
            '"table_name"."text" AS "text", ' .
            '"table_name"."date" AS "date", ' .
            '"table_name"."datetime" AS "datetime", ' .
            '"table_name"."numbers" AS "numbers" ' .
            'FROM "table_name" ' .
            'WHERE "table_name"."int" = ? ' .
            'LIMIT ?',
            array( 3, 1 ),
            array( static::$sampleData )
        );

        $structure = $mapper->find( 3 );

        $this->assertInstanceOf(
            'Zork\Model\Mapper\DbAware\TestReadOnlyStructure',
            $structure
        );

        $this->assertSame( 3, $structure->int );
        $this->assertSame( false, $structure->bool );
        $this->assertSame( 3.14, $structure->float );
    }

    /**
     * Test findOne()
     */
    public function testFindOne()
    {
        /* @var $mapper TestReadOnlyMapper */
        $mapper = $this->getMockedDbAdapterAware( new TestReadOnlyMapper );

        $this->expectStatementSql(
            'SELECT "table_name"."int" AS "int", ' .
            '"table_name"."bool" AS "bool", ' .
            '"table_name"."float" AS "float", ' .
            '"table_name"."string" AS "string", ' .
            '"table_name"."text" AS "text", ' .
            '"table_name"."date" AS "date", ' .
            '"table_name"."datetime" AS "datetime", ' .
            '"table_name"."numbers" AS "numbers" ' .
            'FROM "table_name" ' .
            'WHERE "bool" = ? ' .
            'ORDER BY "float" ASC ' .
            'LIMIT ?',
            array( 'f', 1 ),
            array( static::$sampleData )
        );

        $structure = $mapper->findOne( array(
            'bool'  => 'f',
        ), array(
            'float' => 'ASC'
        ) );

        $this->assertInstanceOf(
            'Zork\Model\Mapper\DbAware\TestReadOnlyStructure',
            $structure
        );

        $this->assertSame( 3, $structure->int );
        $this->assertSame( false, $structure->bool );
        $this->assertSame( 3.14, $structure->float );
    }

    /**
     * Test findOne() returns no result
     */
    public function testFindNoOne()
    {
        /* @var $mapper TestReadOnlyMapper */
        $mapper = $this->getMockedDbAdapterAware( new TestReadOnlyMapper );

        $this->expectStatementSql(
            'SELECT "table_name"."int" AS "int", ' .
            '"table_name"."bool" AS "bool", ' .
            '"table_name"."float" AS "float", ' .
            '"table_name"."string" AS "string", ' .
            '"table_name"."text" AS "text", ' .
            '"table_name"."date" AS "date", ' .
            '"table_name"."datetime" AS "datetime", ' .
            '"table_name"."numbers" AS "numbers" ' .
            'FROM "table_name" ' .
            'WHERE "bool" = ? ' .
            'ORDER BY "float" ASC ' .
            'LIMIT ?',
            array( 'f', 1 ),
            array()
        );

        $structure = $mapper->findOne( array(
            'bool'  => 'f',
        ), array(
            'float' => 'ASC'
        ) );

        $this->assertNull( $structure );
    }

    /**
     * Test findAll()
     */
    public function testFindAll()
    {
        /* @var $mapper TestReadOnlyMapper */
        $mapper = $this->getMockedDbAdapterAware( new TestReadOnlyMapper );

        $this->expectStatementSql(
            'SELECT "table_name"."int" AS "int", ' .
            '"table_name"."bool" AS "bool", ' .
            '"table_name"."float" AS "float", ' .
            '"table_name"."string" AS "string", ' .
            '"table_name"."text" AS "text", ' .
            '"table_name"."date" AS "date", ' .
            '"table_name"."datetime" AS "datetime", ' .
            '"table_name"."numbers" AS "numbers" ' .
            'FROM "table_name" ' .
            'WHERE "bool" = ? ' .
            'ORDER BY "float" ASC ' .
            'LIMIT ? OFFSET ?',
            array( 'f', 2, 3 ),
            array( static::$sampleData,
                   static::$sampleData )
        );

        $structures = $mapper->findAll( array(
            'bool'  => 'f',
        ), array(
            'float' => 'ASC'
        ), 2, 3 );

        $this->assertCount( 2, $structures );

        foreach ( $structures as $structure )
        {
            $this->assertInstanceOf(
                'Zork\Model\Mapper\DbAware\TestReadOnlyStructure',
                $structure
            );

            $this->assertSame( 3, $structure->int );
            $this->assertSame( false, $structure->bool );
            $this->assertSame( 3.14, $structure->float );
        }
    }

    /**
     * Test findOptions()
     */
    public function testFindOptions()
    {
        /* @var $mapper TestReadOnlyMapper */
        $mapper = $this->getMockedDbAdapterAware( new TestReadOnlyMapper );

        $this->expectStatementSql(
            'SELECT "table_name"."int" AS "int", ' .
            '"table_name"."bool" AS "bool", ' .
            '"table_name"."string" AS "string", ' .
            '"table_name"."text" AS "text", ' .
            '"table_name"."date" AS "date", ' .
            '"table_name"."datetime" AS "datetime" ' .
            'FROM "table_name" ' .
            'WHERE "bool" = ? ' .
            'ORDER BY "float" ASC ' .
            'LIMIT ? OFFSET ?',
            array( 'f', 2, 3 ),
            array( static::$sampleData,
                   static::$sampleData )
        );

        $options = $mapper->findOptions( array(
            'int',
            'optbool' => 'bool',
            array( 'string', 'text' ),
            'optdate' => array( 'date', 'datetime' ),
        ), array(
            'bool'  => 'f',
        ), array(
            'float' => 'ASC'
        ), 2, 3 );

        $this->assertCount( 2, $options );

        foreach ( $options as $option )
        {
            $this->assertEquals(
                array(
                    'int'       => '3',
                    'optbool'   => 'f',
                    'string'    => 'text',
                    'optdate'   => '2014-01-06',
                ),
                $option
            );
        }
    }

    /**
     * Test extract()
     */
    public function testExtract()
    {
        $mapper     = new TestReadOnlyMapper;
        $structure  = new TestReadOnlyStructure( static::$sampleData );
        $iterator   = new \ArrayIterator( static::$sampleData );

        $this->assertEquals( static::$sampleData, $mapper->extract( $structure ) );
        $this->assertEquals( static::$sampleData, $mapper->extract( $iterator ) );
        $this->assertEquals( static::$sampleData, $mapper->extract( static::$sampleData ) );
    }

    /**
     * Test hydrate()
     */
    public function testHydrate()
    {
        $mapper     = new TestReadOnlyMapper;
        $structure  = new TestReadOnlyStructure();
        $object     = (object) array();

        $this->assertEquals( static::$sampleData,
                             $mapper->hydrate( static::$sampleData, $structure )->toArray() );
        $this->assertEquals( (object) static::$sampleData,
                             $mapper->hydrate( static::$sampleData, $object ) );
    }

    /**
     * Test getPaginator()
     */
    public function testGetPaginator()
    {
        /* @var $mapper TestReadOnlyMapper */
        $mapper = $this->getMockedDbAdapterAware( new TestReadOnlyMapper );

        $this->expectStatementSql(
            'SELECT COUNT(1) AS "c" ' .
            'FROM "table_name" ' .
            'WHERE "bool" = ?',
            array( 'f' ),
            array( array(
                'c' => 8
            ) )
        );

        $paginator = $mapper->getPaginator( array(
            'bool'  => 'f',
        ), array(
            'float' => 'ASC'
        ) );

        $paginator->setItemCountPerPage( 5 );

        $this->assertCount( 2, $paginator );
    }

    /**
     * Test isExists()
     */
    public function testIsExists()
    {
        /* @var $mapper TestReadOnlyMapper */
        $mapper = $this->getMockedDbAdapterAware( new TestReadOnlyMapper );

        $this->expectStatementSql(
            'SELECT COUNT(*) AS "count" ' .
            'FROM "table_name" ' .
            'WHERE "bool" = ?',
            array( 'f' ),
            array( array(
                'count' => 2,
            ) )
        );

        $count = $mapper->isExists( array(
            'bool'  => 'f',
        ) );

        $this->assertEquals( 2, $count );
    }

}
