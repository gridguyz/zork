<?php

namespace Zork\Iterator;

use ArrayObject;
use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * CallbackMapIterator test case
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Iterator\CallbackMapIterator
 * @covers Zork\Iterator\MapIterator
 */
class CallbackMapIteratorTest extends TestCase
{

    public $array = array(
        1 => array( '+' => 1, '-' => 2 ),
        2 => array( '+' => 2, '-' => 4 ),
        3 => array( '+' => 3, '-' => 6 ),
        4 => array( '+' => 4, '-' => 8 ),
    );

    /**
     * Test inner iterator
     */
    public function testInnerIterator()
    {
        $iterator = new CallbackMapIterator(
            new ArrayObject( $this->array ),
            array( $this, 'map' )
        );

        $this->assertInstanceOf( 'ArrayIterator', $iterator->getInnerIterator() );
    }

    /**
     * Test count
     */
    public function testCount()
    {
        $iterator = new CallbackMapIterator(
            new ArrayIterator( $this->array ),
            array( $this, 'map' )
        );

        $this->assertCount( 4, $iterator );
    }

    /**
     * Test original keys
     */
    public function testOriginalKeys()
    {
        $iterator = new CallbackMapIterator(
            new ArrayIterator( $this->array ),
            array( $this, 'map' )
        );

        $this->assertSame(
            array( 1 => 0, 2 => 0, 3 => 0, 4 => 0 ),
            iterator_to_array( $iterator )
        );
    }

    /**
     * Test generated keys
     */
    public function testGeneratedKeys()
    {
        $iterator = new CallbackMapIterator(
            new ArrayIterator( $this->array ),
            array( $this, 'map' ),
            CallbackMapIterator::FLAG_GENERATE_KEYS
        );

        $this->assertSame(
            array( 0, 0, 0, 0 ),
            iterator_to_array( $iterator )
        );
    }

    /**
     * Map values (& keys)
     *
     * @param   array   $value
     * @param   int     $key
     * @return  int
     */
    public function map( $value, $key )
    {
        return $key + $value['+'] - $value['-'];
    }

}
