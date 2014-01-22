<?php

namespace Zork\Db\Sql;

use Zend\Db\Adapter\StatementContainer;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * FunctionCallTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FunctionCallTest extends TestCase
{

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        include __DIR__ . '/_files/TestSql92.php';
    }

    /**
     * Test constructor, getters & setters
     */
    public function testConstructorGettersAndSetters()
    {
        $call = new FunctionCall(
            'test name',
            $args = array(
                'arg 1',
                2,
                null,
            ),
            FunctionCall::MODE_RESULT_SET
        );

        $call->resultKey( 'result key' );

        $this->assertEquals( 'test name', $call->getName() );
        $this->assertEquals( 'test name', $call->getFunction() );
        $this->assertEquals( FunctionCall::MODE_RESULT_SET, $call->getMode() );
        $this->assertEquals( 'result key', $call->getResultKey() );
        $this->assertEquals( $args, $call->getRawState( 'arguments' ) );

        $call->arguments(
            array( $args[] = 'added' ),
            FunctionCall::ARGUMENTS_MERGE
        );

        $this->assertEquals( $args, $call->getRawState( 'arguments' ) );
    }

    /**
     * Test getSqlString()
     */
    public function testGetSqlString()
    {
        $platform = new TestSql92;
        $call = new FunctionCall(
            'test_name',
            array(
                'arg 1',
                2,
                null,
                new Expression\FunctionCall(
                    'test_sub',
                    array(
                        'id',
                        'text',
                    ),
                    array(
                        Expression\FunctionCall::TYPE_IDENTIFIER,
                        Expression\FunctionCall::TYPE_VALUE,
                    )
                ),
            )
        );

        $call->resultKey( 'result_key' );

        $this->assertEquals(
            'SELECT "test_name"(\'arg 1\', \'2\', NULL, "test_sub"("id", \'text\')) AS "result_key"',
            $call->getSqlString( $platform )
        );

        $call->mode( FunctionCall::MODE_RESULT_SET );

        $this->assertEquals(
            'SELECT * FROM "test_name"(\'arg 1\', \'2\', NULL, "test_sub"("id", \'text\'))',
            $call->getSqlString( $platform )
        );
    }

    /**
     * Test prepareStatement()
     */
    public function testPrepareStatement()
    {
        $platform   = new TestSql92;
        $driver     = $this->getMock( 'Zend\Db\Adapter\Driver\DriverInterface' );
        $adapter    = $this->getMock( 'Zend\Db\Adapter\AdapterInterface' );

        $adapter->expects( $this->any() )
                ->method( 'getPlatform' )
                ->will( $this->returnValue( $platform ) );

        $adapter->expects( $this->any() )
                ->method( 'getDriver' )
                ->will( $this->returnValue( $driver ) );

        $driver->expects( $this->any() )
               ->method( 'formatParameterName' )
               ->will( $this->returnValue( '?' ) );

        $call = new FunctionCall(
            'test_name',
            array(
                'arg 1',
                2,
                null,
                new Expression\FunctionCall(
                    'test_sub',
                    array(
                        'id',
                        'text',
                    ),
                    array(
                        Expression\FunctionCall::TYPE_IDENTIFIER,
                        Expression\FunctionCall::TYPE_VALUE,
                    )
                ),
            )
        );

        $call->resultKey( 'result_key' );

        $statement1 = new StatementContainer;
        $call->prepareStatement( $adapter, $statement1 );

        $this->assertEquals(
            'SELECT "test_name"(?, ?, ?, "test_sub"("id", ?)) AS "result_key"',
            $statement1->getSql()
        );

        $this->assertEquals(
            array( 'arg 1', '2', null, 'text' ),
            $statement1->getParameterContainer()
                       ->getPositionalArray()
        );

        $call->mode( FunctionCall::MODE_RESULT_SET );

        $statement2 = new StatementContainer;
        $call->prepareStatement( $adapter, $statement2 );

        $this->assertEquals(
            'SELECT * FROM "test_name"(?, ?, ?, "test_sub"("id", ?))',
            $statement2->getSql()
        );

        $this->assertEquals(
            array( 'arg 1', '2', null, 'text' ),
            $statement2->getParameterContainer()
                       ->getPositionalArray()
        );
    }

}
