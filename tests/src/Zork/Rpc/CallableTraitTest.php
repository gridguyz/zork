<?php

namespace Zork\Rpc;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * CallableTraitTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CallableTraitTest extends TestCase
{

    /**
     * @var RpcTest
     */
    public $rpc;

    /**
     * This method is called before the first test of this test class is run
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        include_once __DIR__ . '/_files/RpcTest.php';
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->rpc = new RpcTest;
    }

    /**
     * Test default value in arguments
     */
    public function testDefaultValue()
    {
        $this->assertSame(
            array(
                'bool'      => false,
                'int'       => 0,
                'float'     => 0.0,
                'string'    => '0',
                'array'     => array(),
            ),
            $this->rpc->call( 'defaultValue', array() )
        );

        $this->assertSame(
            array(
                'bool'      => true,
                'int'       => 1,
                'float'     => 1.0,
                'string'    => '1',
                'array'     => array( 1 ),
            ),
            $this->rpc->call( 'defaultValue', array(
                true,
                1,
                1.0,
                '1',
                array( 1 ),
            ) )
        );

        $this->assertSame(
            array(
                'bool'      => true,
                'int'       => 1,
                'float'     => 1.0,
                'string'    => '1',
                'array'     => array( 1 ),
            ),
            $this->rpc->call( 'defaultValue', array(
                'bool'      => true,
                'int'       => 1,
                'float'     => 1.0,
                'string'    => '1',
                'array'     => array( 1 ),
            ) )
        );
    }

    /**
     * Test arguments that allows null
     */
    public function testAllowsNull()
    {
        $this->assertSame(
            array(
                'array'     => null,
                'stdClass'  => null,
            ),
            $this->rpc->call( 'allowsNull', array() )
        );

        $array      = array( 'a' => 1 );
        $stdClass   = (object) array( 'b' => 2 );

        $this->assertSame(
            array(
                'array'     => $array,
                'stdClass'  => $stdClass,
            ),
            $this->rpc->call( 'allowsNull', array(
                $array,
                $stdClass,
            ) )
        );

        $this->assertSame(
            array(
                'array'     => $array,
                'stdClass'  => $stdClass,
            ),
            $this->rpc->call( 'allowsNull', array(
                'array'     => $array,
                'stdClass'  => $stdClass,
            ) )
        );
    }

    /**
     * Test required arguments left out
     *
     * @expectedException   InvalidArgumentException
     */
    public function testEmptyRequired()
    {
        $this->rpc->call( 'required', array() );
    }

    /**
     * Test required arguments left out
     *
     * @expectedException   InvalidArgumentException
     */
    public function testMultiArgument()
    {
        $this->rpc->call( 'required', array(
                           true,
            'required'  => false,
        ) );
    }

    /**
     * Test method in not-callables
     *
     * @expectedException   BadMethodCallException
     */
    public function testNotCallable()
    {
        $this->rpc->call( 'notCallable', array() );
    }

    /**
     * Test method not in only-callables
     *
     * @expectedException   BadMethodCallException
     */
    public function testNotInOnlyCallable()
    {
        $this->rpc->call( 'notInOnlyCallable', array() );
    }

    /**
     * Test method not exists
     *
     * @expectedException   BadMethodCallException
     */
    public function testNotExists()
    {
        $this->rpc->call( 'notExists', array() );
    }

}
