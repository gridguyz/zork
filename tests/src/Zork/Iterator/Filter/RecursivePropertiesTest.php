<?php

namespace Zork\Iterator\Filter;

use RecursiveIteratorIterator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * PropertiesTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Iterator\Filter\RecursiveProperties
 */
class RecursivePropertiesTest extends TestCase
{

    /**
     * @var array
     */
    protected $array = array(
        array( 'foo' => 1, 'bar' => 1, 'baz' => 1 ),
        array( 'foo' => 1, 'bar' => 2, 'baz' => 3, 'children' => array(
            array( 'foo' => 1, 'bar' => 3, 'baz' => 6 ),
            array( 'foo' => 1, 'bar' => 4, 'baz' => 10 ),
        ) ),
        array( 'foo' => '1', 'bar' => '1', 'baz' => '1', 'children' => array(
            array( 'foo' => '1', 'bar' => '2', 'baz' => '3' ),
            array( 'foo' => '1', 'bar' => '3', 'baz' => '6', 'children' => array(
                array( 'foo' => '1', 'bar' => '4', 'baz' => '10' ),
            ) ),
        ) ),
    );

    /**
     * @var \RecursiveArrayIterator
     */
    protected $iterator;

    /**
     *
     * @var RecursiveProperties
     */
    protected $properties;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        require_once __DIR__ . '/_files/RecursiveTestData.php';
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->iterator     = new RecursiveTestData( $this->array );
        $this->properties   = new RecursiveProperties( $this->iterator );
    }

    /**
     * Test inner-iterator
     */
    public function testInnerIterator()
    {
        $this->assertEquals(
            $this->iterator,
            $this->properties
                 ->getInnerIterator()
        );
    }

    /**
     * Test add property
     */
    public function testAddProperty()
    {
        $this->properties
             ->addProperty( 'foo', 1, RecursiveProperties::CMP_IDENTICAL );

        $this->assertAttributeEquals(
            array(
                'foo' => array( RecursiveProperties::CMP_IDENTICAL => 1 ),
            ),
            'properties',
            $this->properties
        );
    }

    /**
     * Test add properties
     */
    public function testAddProperties()
    {
        $this->properties
             ->addProperties( array(
                 'foo ='    => 1,
                 'bar ~'    => '/2|3/',
                 'baz <>'   => 3,
             ) );

        $this->assertAttributeEquals(
            array(
                'foo' => array( RecursiveProperties::CMP_EQUAL      => 1 ),
                'bar' => array( RecursiveProperties::CMP_REGEXP     => '/2|3/' ),
                'baz' => array( RecursiveProperties::CMP_NOT_EQUAL  => 3 ),
            ),
            'properties',
            $this->properties
        );
    }

    /**
     * Test filter: equal
     */
    public function testFilterEqual()
    {
        $this->properties
             ->addProperties( array(
                    'foo =' => '1',
                    'bar =' => '2',
                    'baz =' => '3',
                ) );

        $count      = 0;
        $recursive  = new RecursiveIteratorIterator(
            $this->properties,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ( $recursive as $item )
        {
            $count++;

            $this->assertEquals( '1', $item['foo'] );
            $this->assertEquals( '2', $item['bar'] );
            $this->assertEquals( '3', $item['baz'] );
        }

        $this->assertEquals( 1, $count );
    }

    /**
     * Test filter: identical
     */
    public function testFilterIdentical()
    {
        $this->properties
             ->addProperties( array(
                    'foo ===' => 1,
                    'bar ===' => 2,
                    'baz ===' => 3,
                ) );

        $count      = 0;
        $recursive  = new RecursiveIteratorIterator(
            $this->properties,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ( $recursive as $item )
        {
            $count++;

            $this->assertSame( 1, $item['foo'] );
            $this->assertSame( 2, $item['bar'] );
            $this->assertSame( 3, $item['baz'] );
        }

        $this->assertEquals( 1, $count );
    }

    /**
     * Test filter: regexp
     */
    public function testFilterRegexp()
    {
        $this->properties
             ->addProperties( array(
                    'foo ~' => '/^1$/',
                    'bar ~' => '/^1|4$/',
                    'baz ~' => '/^1/',
                ) );

        $count      = 0;
        $recursive  = new RecursiveIteratorIterator(
            $this->properties,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ( $recursive as $item )
        {
            $count++;

            $this->assertRegExp( '/^1$/',   (string) $item['foo'] );
            $this->assertRegExp( '/^1|4$/', (string) $item['bar'] );
            $this->assertRegExp( '/^1/',    (string) $item['baz'] );
        }

        $this->assertEquals( 2, $count );
    }

    /**
     * Test filter: not-equal
     */
    public function testFilterNotEqual()
    {
        $this->properties
             ->addProperties( array(
                    'foo <>' => 2,
                    'bar <>' => 2,
                    'baz !=' => 6,
                ) );

        $count      = 0;
        $recursive  = new RecursiveIteratorIterator(
            $this->properties,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ( $recursive as $item )
        {
            $count++;

            $this->assertNotEquals( 2, $item['foo'] );
            $this->assertNotEquals( 2, $item['bar'] );
            $this->assertNotEquals( 6, $item['baz'] );
        }

        $this->assertEquals( 2, $count );
    }

    /**
     * Test filter: not-identical
     */
    public function testFilterNotIdentical()
    {
        $this->properties
             ->addProperties( array(
                    'foo !==' => 2,
                    'bar !==' => 2,
                    'baz !==' => 6,
                ) );

        $count      = 0;
        $recursive  = new RecursiveIteratorIterator(
            $this->properties,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ( $recursive as $item )
        {
            $count++;

            $this->assertNotSame( 2, $item['foo'] );
            $this->assertNotSame( 2, $item['bar'] );
            $this->assertNotSame( 6, $item['baz'] );
        }

        $this->assertEquals( 5, $count );
    }

    /**
     * Test filter: not-regexp
     */
    public function testFilterNotReqexp()
    {
        $this->properties
             ->addProperties( array(
                    'foo !~' => '/^2|3$/',
                    'bar !~' => '/^2|3$/',
                    'baz !~' => '/^2|3$/',
                ) );

        $count      = 0;
        $recursive  = new RecursiveIteratorIterator(
            $this->properties,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ( $recursive as $item )
        {
            $count++;

            $this->assertNotRegExp( '/^2|3$/', (string) $item['foo'] );
            $this->assertNotRegExp( '/^2|3$/', (string) $item['bar'] );
            $this->assertNotRegExp( '/^2|3$/', (string) $item['baz'] );
        }

        $this->assertEquals( 2, $count );
    }

    /**
     * Test filter: greater-than
     */
    public function testFilterGreaterThan()
    {
        $this->properties
             ->addProperties( array(
                    'foo >' => 0,
                    'bar >' => 1,
                    'baz >' => 2,
                ) );

        $count      = 0;
        $recursive  = new RecursiveIteratorIterator(
            $this->properties,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ( $recursive as $item )
        {
            $count++;

            $this->assertGreaterThan( 0, $item['foo'] );
            $this->assertGreaterThan( 1, $item['bar'] );
            $this->assertGreaterThan( 2, $item['baz'] );
        }

        $this->assertEquals( 3, $count );
    }

    /**
     * Test filter: greater-equal
     */
    public function testFilterGreaterEqual()
    {
        $this->properties
             ->addProperties( array(
                    'foo >=' => 1,
                    'bar >=' => 2,
                    'baz >=' => 3,
                ) );

        $count      = 0;
        $recursive  = new RecursiveIteratorIterator(
            $this->properties,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ( $recursive as $item )
        {
            $count++;

            $this->assertGreaterThanOrEqual( 1, $item['foo'] );
            $this->assertGreaterThanOrEqual( 2, $item['bar'] );
            $this->assertGreaterThanOrEqual( 3, $item['baz'] );
        }

        $this->assertEquals( 3, $count );
    }

    /**
     * Test filter: lesser-than
     */
    public function testFilterLesserThan()
    {
        $this->properties
             ->addProperties( array(
                    'foo <' => 2,
                    'bar <' => 2,
                    'baz <' => 2,
                ) );

        $count      = 0;
        $recursive  = new RecursiveIteratorIterator(
            $this->properties,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ( $recursive as $item )
        {
            $count++;

            $this->assertLessThan( 2, $item['foo'] );
            $this->assertLessThan( 2, $item['bar'] );
            $this->assertLessThan( 2, $item['baz'] );
        }

        $this->assertEquals( 2, $count );
    }

    /**
     * Test filter: lesser-equal
     */
    public function testFilterLesserEqual()
    {
        $this->properties
             ->addProperties( array(
                    'foo <=' => 1,
                    'bar <=' => 1,
                    'baz <=' => 1,
                ) );

        $count      = 0;
        $recursive  = new RecursiveIteratorIterator(
            $this->properties,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ( $recursive as $item )
        {
            $count++;

            $this->assertLessThanOrEqual( 1, $item['foo'] );
            $this->assertLessThanOrEqual( 1, $item['bar'] );
            $this->assertLessThanOrEqual( 1, $item['baz'] );
        }

        $this->assertEquals( 2, $count );
    }

    /**
     * Test filter: callback
     */
    public function testFilterCallback()
    {
        $this->properties
             ->addProperties( array(
                    'foo ()' => array( $this, 'callbackFoo' ),
                    'bar ()' => array( $this, 'callbackBar' ),
                    'baz ()' => array( $this, 'callbackBaz' ),
                ) );

        $count      = 0;
        $recursive  = new RecursiveIteratorIterator(
            $this->properties,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ( $recursive as $item )
        {
            $count++;

            $this->assertTrue( $this->callbackFoo( $item['foo'] ) );
            $this->assertTrue( $this->callbackBar( $item['bar'] ) );
            $this->assertTrue( $this->callbackBaz( $item['baz'] ) );
        }

        $this->assertEquals( 3, $count );
    }

    /**
     * Callback for foo
     *
     * @param   mixed   $value
     * @return  bool
     */
    public function callbackFoo( $value )
    {
        return '1' === $value;
    }

    /**
     * Callback for bar
     *
     * @param   mixed   $value
     * @return  bool
     */
    public function callbackBar( $value )
    {
        return $value < 4;
    }

    /**
     * Callback for bar
     *
     * @param   mixed   $value
     * @return  bool
     */
    public function callbackBaz( $value )
    {
        return $value < 8;
    }

}
