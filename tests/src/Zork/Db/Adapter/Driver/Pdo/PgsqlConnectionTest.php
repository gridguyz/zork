<?php

namespace Zork\Db\Adapter\Driver\Pdo;

use PDO;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * PgsqlConnectionTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class PgsqlConnectionTest extends TestCase
{

    /**
     * Test __construct()
     */
    public function testConstructor()
    {
        // test connection construction with
        // nestable transactions & multiple schemas

        $connection1 = $this->getMock(
            'Zork\Db\Adapter\Driver\Pdo\PgsqlConnection',
            array(
                'setNestableTransactionsEnabled',
                'setCurrentSchemas',
            ),
            array(),
            '',
            false,
            false
        );

        $connection1->expects( $this->once() )
                    ->method( 'setNestableTransactionsEnabled' )
                    ->with( true )
                    ->will( $this->returnSelf() );

        $connection1->expects( $this->once() )
                    ->method( 'setCurrentSchemas' )
                    ->with( array( 'foo', 'bar' ) )
                    ->will( $this->returnSelf() );

        $connection1->__construct( array(
            'nestableTransactions'  => true,
            'schema'                => array( 'foo', 'bar' ),
        ) );

        // test connection construction with a single schema

        $connection2 = $this->getMock(
            'Zork\Db\Adapter\Driver\Pdo\PgsqlConnection',
            array(
                'setCurrentSchema',
            ),
            array(),
            '',
            false,
            false
        );

        $connection2->expects( $this->once() )
                    ->method( 'setCurrentSchema' )
                    ->with( 'foo' )
                    ->will( $this->returnValue( 'bar' ) );

        $connection2->__construct( array(
            'schema'    => 'foo',
        ) );

        $connection3 = $this->getMock(
            'Zork\Db\Adapter\Driver\Pdo\PgsqlConnection',
            array(
                'setNestableTransactionsEnabled',
            ),
            array(),
            '',
            false,
            false
        );

        // test connection construction without nestable transactions

        $connection3->expects( $this->once() )
                    ->method( 'setNestableTransactionsEnabled' )
                    ->with( false )
                    ->will( $this->returnSelf() );

        $connection3->__construct( array(
            'nestableTransactions'  => false,
        ) );
    }

    /**
     * Test getCurrentSchemas() without catalog
     */
    public function testGetCurrentSchemasWithoutCatalog()
    {
        $connection = $this->getMock(
            'Zork\Db\Adapter\Driver\Pdo\PgsqlConnection',
            array(
                'isConnected',
                'connect',
                'getResource',
            ),
            array(),
            '',
            false,
            false
        );

        $resource = $this->getMock(
            'Zork\Test\Pdo\Pdo',
            array( 'query' ),
            array(),
            '',
            true,
            false
        );

        $result = $this->getMock(
            'Zork\Test\Pdo\Statement',
            array( 'fetchAll' ),
            array(),
            '',
            true,
            false
        );

        $connection->expects( $this->once() )
                   ->method( 'isConnected' )
                   ->will( $this->returnValue( false ) );

        $connection->expects( $this->once() )
                   ->method( 'connect' )
                   ->will( $this->returnSelf() );

        $connection->expects( $this->any() )
                   ->method( 'getResource' )
                   ->will( $this->returnValue( $resource ) );

        $resource->expects( $this->once() )
                 ->method( 'query' )
                 ->with( 'SELECT UNNEST( CURRENT_SCHEMAS( FALSE ) )' )
                 ->will( $this->returnValue( $result ) );

        $result->expects( $this->once() )
               ->method( 'fetchAll' )
               ->with( PDO::FETCH_COLUMN )
               ->will( $this->returnValue( array( 'foo', 'bar' ) ) );

        $this->assertEquals(
            array( 'foo', 'bar' ),
            $connection->getCurrentSchemas()
        );
    }

    /**
     * Test getCurrentSchemas() with catalog
     */
    public function testGetCurrentSchemasWithCatalog()
    {
        $connection = $this->getMock(
            'Zork\Db\Adapter\Driver\Pdo\PgsqlConnection',
            array(
                'isConnected',
                'connect',
                'getResource',
            ),
            array(),
            '',
            false,
            false
        );

        $resource = $this->getMock(
            'Zork\Test\Pdo\Pdo',
            array( 'query' ),
            array(),
            '',
            true,
            false
        );

        $result = $this->getMock(
            'Zork\Test\Pdo\Statement',
            array( 'fetchAll' ),
            array(),
            '',
            true,
            false
        );

        $connection->expects( $this->once() )
                   ->method( 'isConnected' )
                   ->will( $this->returnValue( true ) );

        $connection->expects( $this->never() )
                   ->method( 'connect' );

        $connection->expects( $this->any() )
                   ->method( 'getResource' )
                   ->will( $this->returnValue( $resource ) );

        $resource->expects( $this->once() )
                 ->method( 'query' )
                 ->with( 'SELECT UNNEST( CURRENT_SCHEMAS( TRUE ) )' )
                 ->will( $this->returnValue( $result ) );

        $result->expects( $this->once() )
               ->method( 'fetchAll' )
               ->with( PDO::FETCH_COLUMN )
               ->will( $this->returnValue( array( 'foo', 'bar', 'pg_catalog' ) ) );

        $this->assertEquals(
            array( 'foo', 'bar', 'pg_catalog' ),
            $connection->getCurrentSchemas( true )
        );
    }

    /**
     * Test setCurrentSchemas()
     */
    public function testSetCurrentSchemas()
    {
        $connection = $this->getMock(
            'Zork\Db\Adapter\Driver\Pdo\PgsqlConnection',
            array(
                'isConnected',
                'connect',
                'getResource',
            ),
            array(),
            '',
            false,
            false
        );

        $resource = $this->getMock(
            'Zork\Test\Pdo\Pdo',
            array( 'query' ),
            array(),
            '',
            true,
            false
        );

        $connection->expects( $this->once() )
                   ->method( 'isConnected' )
                   ->will( $this->returnValue( false ) );

        $connection->expects( $this->once() )
                   ->method( 'connect' )
                   ->will( $this->returnSelf() );

        $connection->expects( $this->any() )
                   ->method( 'getResource' )
                   ->will( $this->returnValue( $resource ) );

        $resource->expects( $this->once() )
                 ->method( 'query' )
                 ->with( 'SET search_path TO "foo bar", "awk""ward"' )
                 ->will( $this->returnValue( null ) );

        $connection->setCurrentSchemas( array( 'foo bar', 'awk"ward' ) );
    }

    /**
     * Test setCurrentSchema()
     */
    public function testSetCurrentSchema()
    {
        $connection = $this->getMock(
            'Zork\Db\Adapter\Driver\Pdo\PgsqlConnection',
            array(
                'getCurrentSchemas',
                'setCurrentSchemas',
            ),
            array(),
            '',
            false,
            false
        );

        $connection->expects( $this->any() )
                   ->method( 'getCurrentSchemas' )
                   ->will( $this->returnValue( array( 'old', 'foo' ) ) );

        $connection->expects( $this->once() )
                   ->method( 'setCurrentSchemas' )
                   ->with( array( 'new', 'foo' ) )
                   ->will( $this->returnSelf() );

        $this->assertEquals( 'old', $connection->setCurrentSchema( 'old' ) );
        $this->assertEquals( 'old', $connection->setCurrentSchema( 'new' ) );
    }

    /**
     * Test nestable transactions
     */
    public function testNestableTransactions()
    {
        $isConnected = false;
        $connection = $this->getMock(
            'Zork\Db\Adapter\Driver\Pdo\PgsqlConnection',
            array(
                'isConnected',
                'connect',
                'getResource',
            ),
            array(),
            '',
            false,
            false
        );

        $resource = $this->getMock(
            'Zork\Test\Pdo\Pdo',
            array(
                'beginTransaction',
                'commit',
                'rollback',
                'exec',
            ),
            array(),
            '',
            true,
            false
        );

        $this->assertEquals( 0, $connection->getTransactionnestingLevel() );
        $this->assertFalse( $connection->isNestableTransactionsEnabled() );
        $this->assertFalse( $connection->getNestableTransactionsEnabled() );

        $connection->setNestableTransactionsEnabled( true );
        $this->assertTrue( $connection->isNestableTransactionsEnabled() );
        $this->assertTrue( $connection->getNestableTransactionsEnabled() );

        $connection->expects( $this->any() )
                   ->method( 'isConnected' )
                   ->will( $this->returnCallback( function () use ( & $isConnected ) {
                       return (bool) $isConnected;
                   } ) );

        $connection->expects( $this->any() )
                   ->method( 'connect' )
                   ->will( $this->returnSelf() );

        $connection->expects( $this->any() )
                   ->method( 'getResource' )
                   ->will( $this->returnValue( $resource ) );

        $propertyResource = new \ReflectionProperty(
            'Zend\Db\Adapter\Driver\Pdo\Connection',
            'resource'
        );

        $propertyResource->setAccessible( true );
        $propertyResource->setValue( $connection, $resource );

        $propertyUniqid = new \ReflectionProperty(
            'Zork\Db\Adapter\Driver\Pdo\PgsqlConnection',
            'transactionSavepointUniqueId'
        );

        $propertyUniqid->setAccessible( true );
        $propertyUniqid->setValue( $connection, $uniqid = uniqid() );

        $resource->expects( $this->exactly( 3 ) )
                 ->method( 'beginTransaction' )
                 ->will( $this->returnValue( true ) );

        $resource->expects( $this->exactly( 1 ) )
                 ->method( 'commit' )
                 ->will( $this->returnValue( true ) );

        $resource->expects( $this->exactly( 2 ) )
                 ->method( 'rollback' )
                 ->will( $this->returnValue( true ) );

        $resource->expects( $this->at( 1 ) )
                 ->method( 'exec' )
                 ->with( "SAVEPOINT zorksavepoint_{$uniqid}_1" )
                 ->will( $this->returnValue( 0 ) );

        $resource->expects( $this->at( 2 ) )
                 ->method( 'exec' )
                 ->with( "SAVEPOINT zorksavepoint_{$uniqid}_2" )
                 ->will( $this->returnValue( 0 ) );

        $resource->expects( $this->at( 3 ) )
                 ->method( 'exec' )
                 ->with( "RELEASE SAVEPOINT zorksavepoint_{$uniqid}_2" )
                 ->will( $this->returnValue( 0 ) );

        $resource->expects( $this->at( 4 ) )
                 ->method( 'exec' )
                 ->with( "ROLLBACK TO SAVEPOINT zorksavepoint_{$uniqid}_1" )
                 ->will( $this->returnValue( 0 ) );

        $resource->expects( $this->at( 7 ) )
                 ->method( 'exec' )
                 ->with( "SAVEPOINT zorksavepoint_{$uniqid}_1" )
                 ->will( $this->returnValue( 0 ) );

        $resource->expects( $this->at( 8 ) )
                 ->method( 'exec' )
                 ->with( "SAVEPOINT zorksavepoint_{$uniqid}_2" )
                 ->will( $this->returnValue( 0 ) );

        $resource->expects( $this->at( 9 ) )
                 ->method( 'exec' )
                 ->with( "ROLLBACK TO SAVEPOINT zorksavepoint_{$uniqid}_2" )
                 ->will( $this->returnValue( 0 ) );

        $resource->expects( $this->at( 10 ) )
                 ->method( 'exec' )
                 ->with( "RELEASE SAVEPOINT zorksavepoint_{$uniqid}_1" )
                 ->will( $this->returnValue( 0 ) );

        $this->assertEquals( 0, $connection->getTransactionnestingLevel() );

        $connection->beginTransaction();
            $this->assertEquals( 1, $connection->getTransactionnestingLevel() );
            $connection->beginTransaction();
                $this->assertEquals( 2, $connection->getTransactionnestingLevel() );
                $connection->beginTransaction();
                    $this->assertEquals( 3, $connection->getTransactionnestingLevel() );
                $connection->commit();
                $this->assertEquals( 2, $connection->getTransactionnestingLevel() );
            $connection->rollBack();
            $this->assertEquals( 1, $connection->getTransactionnestingLevel() );
        $connection->commit();

        $this->assertEquals( 0, $connection->getTransactionnestingLevel() );

        $connection->beginTransaction();
            $this->assertEquals( 1, $connection->getTransactionnestingLevel() );
            $connection->beginTransaction();
                $this->assertEquals( 2, $connection->getTransactionnestingLevel() );
                $connection->beginTransaction();
                    $this->assertEquals( 3, $connection->getTransactionnestingLevel() );
                $connection->rollBack();
                $this->assertEquals( 2, $connection->getTransactionnestingLevel() );
            $connection->commit();
            $this->assertEquals( 1, $connection->getTransactionnestingLevel() );
        $isConnected = true;
        $connection->rollBack();
        $isConnected = false;

        $this->assertEquals( 0, $connection->getTransactionnestingLevel() );

        $connection->beginTransaction();
            $this->assertEquals( 1, $connection->getTransactionnestingLevel() );
            $connection->beginTransaction();
                $this->assertEquals( 2, $connection->getTransactionnestingLevel() );
        $isConnected = true;
        $connection->resetTransactions();
        $isConnected = false;

        $this->assertEquals( 0, $connection->getTransactionnestingLevel() );

        $this->setExpectedException(
            'Zork\Db\Adapter\Driver\Pdo\UnbalancedNestedTransactionsException'
        );

        $connection->commit();
    }

    /**
     * Test simple transactions
     */
    public function testSimpleTransactions()
    {
        $connection = $this->getMock(
            'Zork\Db\Adapter\Driver\Pdo\PgsqlConnection',
            array(
                'isConnected',
                'connect',
                'getResource',
            ),
            array(),
            '',
            false,
            false
        );

        $resource = $this->getMock(
            'Zork\Test\Pdo\Pdo',
            array(
                'beginTransaction',
                'commit',
                'rollback',
                'exec',
            ),
            array(),
            '',
            true,
            false
        );

        $connection->expects( $this->any() )
                   ->method( 'isConnected' )
                   ->will( $this->returnValue( true ) );

        $connection->expects( $this->any() )
                   ->method( 'connect' )
                   ->will( $this->returnSelf() );

        $connection->expects( $this->any() )
                   ->method( 'getResource' )
                   ->will( $this->returnValue( $resource ) );

        $propertyResource = new \ReflectionProperty(
            'Zend\Db\Adapter\Driver\Pdo\Connection',
            'resource'
        );

        $propertyResource->setAccessible( true );
        $propertyResource->setValue( $connection, $resource );

        $resource->expects( $this->exactly( 3 ) )
                 ->method( 'beginTransaction' )
                 ->will( $this->returnValue( true ) );

        $resource->expects( $this->exactly( 1 ) )
                 ->method( 'commit' )
                 ->will( $this->returnValue( true ) );

        $resource->expects( $this->exactly( 2 ) )
                 ->method( 'rollback' )
                 ->will( $this->returnValue( true ) );

        $connection->beginTransaction();
        $connection->commit();

        $connection->beginTransaction();
        $connection->rollBack();

        $connection->beginTransaction();
        $connection->resetTransactions();
    }

}
