<?php

namespace Zork\Data;

use ArrayIterator;
use Zork\Stdlib\DateTime;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * TransformTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TransformTest extends TestCase
{

    /**
     * Test transform to string
     */
    public function testIdentical()
    {
        $this->assertSame( null, Transform::identical( null ) );
        $this->assertSame( false, Transform::identical( false ) );
        $this->assertSame( 0, Transform::identical( 0 ) );
        $this->assertSame( '0', Transform::identical( '0' ) );
    }

    /**
     * Test transform to string
     */
    public function testToString()
    {
        $this->assertSame( '0', Transform::toString( 0 ) );
    }

    /**
     * Test transform to integer
     */
    public function testToInteger()
    {
        $this->assertSame( 0, Transform::toInteger( '0' ) );
    }

    /**
     * Test transform to float
     */
    public function testToFloat()
    {
        $this->assertSame( 1.0, Transform::toFloat( '1.0' ) );
    }

    /**
     * Test transform to boolean
     */
    public function testToBoolean()
    {
        $this->assertSame( true, Transform::toBoolean( '1' ) );
        $this->assertSame( false, Transform::toBoolean( '' ) );
        $this->assertSame( false, Transform::toBoolean( '0' ) );
    }

    /**
     * Test transform to array
     */
    public function testToArray()
    {
        $array = array( null, true, 2, 3.0, '4' );

        $this->assertSame( $array, Transform::toArray( $array ) );
        $this->assertSame( $array, Transform::toArray( (object) $array ) );
        $this->assertSame( $array, Transform::toArray( new ArrayIterator( $array ) ) );
        $this->assertSame( array(), Transform::toArray( null ) );
        $this->assertSame( array( 1 ), Transform::toArray( 1 ) );
    }

    /**
     * Test transform to DateTime
     */
    public function testToDateTime()
    {
        $this->assertInstanceOf( 'Zork\Stdlib\DateTime', Transform::toDateTime( new DateTime ) );
        $this->assertInstanceOf( 'Zork\Stdlib\DateTime', Transform::toDateTime( new \DateTime ) );
        $this->assertInstanceOf( 'Zork\Stdlib\DateTime', Transform::toDateTime( time() ) );
        $this->assertInstanceOf( 'Zork\Stdlib\DateTime', Transform::toDateTime( date( 'Y-m-d H:i:s' ) ) );
        $this->assertInstanceOf( 'Zork\Stdlib\DateTime', Transform::toDateTime( date( 'Y-m-dTH:i:s' ) ) );
        $this->assertInstanceOf( 'Zork\Stdlib\DateTime', Transform::toDateTime( date( DATE_ISO8601 ) ) );
        $this->assertInstanceOf( 'Zork\Stdlib\DateTime', Transform::toDateTime( date( DATE_W3C ) ) );
        $this->assertInstanceOf( 'Zork\Stdlib\DateTime', Transform::toDateTime( $this ) );
    }

    /**
     * Test transform to callable
     */
    public function testToCallable()
    {
        $callable = Transform::toCallable( $this );
        $this->assertSame( M_PI, $callable() );

        $callable = Transform::toCallable( __CLASS__ );
        $this->assertSame( M_PI, $callable() );

        $callable = Transform::toCallable( array( $this, '__invoke' ) );
        $this->assertSame( M_PI, $callable() );

        $callable = Transform::toCallable( 'floatval' );
        $this->assertSame( M_PI, $callable( M_PI ) );
    }

    /**
     * Test transform on not callable
     *
     * @expectedException   InvalidArgumentException
     */
    public function testNotCallable()
    {
        Transform::toCallable( 'function_that_not_exists' );
    }

    /**
     * Helper for datetimes
     *
     * @return  string
     */
    public function __toString()
    {
        return date( 'Y-m-d H:i:s' );
    }

    /**
     * Helper for callables
     *
     * @return float
     */
    public function __invoke()
    {
        return M_PI;
    }

}
