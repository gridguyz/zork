<?php

namespace Zork\Factory;

use Zend\Config\Config;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * BuilderTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Factory\Builder
 * @covers Zork\Factory\FactoryTrait
 * @covers Zork\Factory\AdapterAbstract
 */
class BuilderTest extends TestCase
{

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var BuilderTest\Factory
     */
    protected $factory;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        include_once __DIR__ . '/_files/BuilderTestClasses.php';
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->builder = Builder::factory( array() );
        $this->factory = new BuilderTest\Factory( $this->builder );
        $this->factory
             ->registerFactory( array( __CLASS__ . '\\Dependecy', 'Countable' ) )
             ->registerAdapter( 'adapter1', __CLASS__ . '\\Adapter1' )
             ->registerAdapter( 'adapter2', __CLASS__ . '\\Adapter2' );
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->factory
             ->unregisterFactory();

        $this->builder
             ->unregisterAll();

        $this->builder = null;
        $this->factory = null;
    }

    /**
     * Test register to non-existing factory
     *
     * @expectedException   Zork\Factory\Exception\ExceptionInterface
     */
    public function testRegisterToNonExistingFactory()
    {
        $this->builder
             ->registerAdapter( 'NonExsitentFactory', 'adapter0', 'stdClass' );
    }

    /**
     * Test register non-existing class as adapter
     *
     * @expectedException   Zork\Factory\Exception\ExceptionInterface
     */
    public function testRegisterNonExistingClassAsAdapter()
    {
        $this->factory
             ->registerAdapter( 'adapter0', 'NonExsitentAdapter' );
    }

    /**
     * Test register stdClass as adapter
     *
     * @expectedException   Zork\Factory\Exception\ExceptionInterface
     */
    public function testRegisterStdClassAsAdapter()
    {
        $this->factory
             ->registerAdapter( 'adapter0', 'stdClass' );
    }

    /**
     * Test register invalid adapter
     *
     * @expectedException   Zork\Factory\Exception\ExceptionInterface
     */
    public function testRegisterInvalidAdapterNotExtendsDependecy()
    {
        $this->factory
             ->registerAdapter( 'adapter0', __CLASS__ . '\\InvalidAdapterNotAdapter' );
    }

    /**
     * Test register invalid adapter
     *
     * @expectedException   Zork\Factory\Exception\ExceptionInterface
     */
    public function testRegisterInvalidAdapterNotImplementsDependecy()
    {
        $this->factory
             ->registerAdapter( 'adapter0', __CLASS__ . '\\InvalidAdapterNotCountable' );
    }

    /**
     * Test factory
     */
    public function testFactory()
    {
        $this->assertTrue(
            $this->builder
                 ->isFactoryRegistered( $this->factory )
        );

        $this->assertTrue(
            $this->factory
                 ->isFactoryRegistered()
        );

        $this->assertContains(
            get_class( $this->factory ),
            $this->builder
                 ->getRegisteredFactories()
        );

        $this->assertSame(
            $this->builder
                 ->getRegisteredAdapters( $this->factory ),
            $this->factory
                 ->getRegisteredAdapters()
        );

        $this->assertEmpty(
            $this->builder
                 ->getRegisteredAdapters( 'NonExistingFactory' )
        );

        $this->assertInstanceOf(
            __CLASS__ . '\\Adapter1',
            $this->factory
                 ->factory( 'adapter1' )
        );

        $this->assertInstanceOf(
            __CLASS__ . '\\Adapter2',
            $this->factory
                 ->factory( 'adapter2' )
        );

        $this->assertInstanceOf(
            __CLASS__ . '\\Adapter1',
            $this->factory
                 ->factory( 'adapter1', array(
                'param1'    => 'value1',
            ) )
        );

        $this->assertInstanceOf(
            __CLASS__ . '\\Adapter2',
            $this->factory
                 ->factory( array(
                'adapter'   => 'adapter2',
                'param2'    => 'value2',
            ) )
        );

        $this->assertInstanceOf(
            __CLASS__ . '\\Adapter1',
            $this->factory
                 ->factory( array(
                'param1'    => 'value1',
            ) )
        );

        $this->assertInstanceOf(
            __CLASS__ . '\\Adapter2',
            $this->factory
                 ->factory( array(
                'param2'    => 'value2',
            ) )
        );

        $this->assertSame(
            null,
            $this->factory
                 ->factory( array() )
        );

        $this->factory
             ->unregisterAdapter( 'adapter1' )
             ->unregisterAdapter( 'adapter2' );

        $this->assertSame(
            null,
            $this->factory
                 ->factory( 'NonExistingAdapter' )
        );
    }

    /**
     * Test forced adapter
     */
    public function testForce()
    {
        $this->assertFalse(
            $this->factory
                 ->isAdapterRegistered( 'adapterForce' )
        );

        $this->assertSame(
            null,
            $this->factory
                 ->factory( 'adapterForce' )
        );

        $this->factory
             ->registerAdapter( 'adapterForce', __CLASS__ . '\\AdapterForce' );

        $this->assertInstanceOf(
            __CLASS__ . '\\AdapterForce',
            $this->factory
                 ->factory( array(
                'param1'    => 'value1',
            ) )
        );

        $this->assertInstanceOf(
            __CLASS__ . '\\AdapterForce',
            $this->factory
                 ->factory( array(
                'param2'    => 'value2',
            ) )
        );

        $adapter = $this->factory
                        ->factory( array(
                            'param1' => 'value1',
                        ), array(
                            'param2' => 'value2',
                        ) );

        $this->assertInstanceOf( __CLASS__ . '\\AdapterForce', $adapter );
        $this->assertSame( 'value1', $adapter->getOption( 'param1' ) );
        $this->assertSame( 'value2', $adapter->getOption( 'param2' ) );

        $adapter = null;
        $adapter = $this->factory
                        ->factory( new Config( array(
                            'param1' => 'value1',
                        ) ), new Config( array(
                            'param2' => 'value2',
                        ) ) );

        $this->assertInstanceOf( __CLASS__ . '\\AdapterForce', $adapter );
        $this->assertSame( 'value1', $adapter->getOption( 'param1' ) );
        $this->assertSame( 'value2', $adapter->getOption( 'param2' ) );

        $this->assertTrue(
            $this->factory
                 ->isAdapterRegistered( $adapter )
        );

        $this->factory
             ->unregisterAdapter( 'adapterForce' );

        $this->assertSame(
            null,
            $this->factory
                 ->factory( 'adapterForce' )
        );

        $this->assertFalse(
            $this->builder
                 ->isAdapterRegistered( $this->factory, $adapter )
        );

        $this->assertFalse(
            $this->builder
                 ->isAdapterRegistered( 'NonExistingFactory', 'NonExistingAdapter' )
        );
    }

    public function testChangeBuilder()
    {
        $builder = Builder::factory( array(
            __CLASS__ . '\\Factory' => array(
                'dependency' => array(
                    __CLASS__ . '\\Dependecy',
                    'Countable'
                ),
                'adapter'   => array(
                    'adapter1' => __CLASS__ . '\\Adapter1',
                    'adapter2' => __CLASS__ . '\\Adapter2',
                ),
            ),
        ) );

        $this->assertSame(
            $this->builder,
            $this->factory
                 ->getFactoryBuilder()
        );

        $this->factory
             ->setFactoryBuilder( $builder );

        $this->assertNotSame(
            $this->builder,
            $this->factory
                 ->getFactoryBuilder()
        );

        $this->builder = null;
        $this->builder = $builder;
    }

}
