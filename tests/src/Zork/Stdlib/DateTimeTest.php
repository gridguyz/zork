<?php

namespace Zork\Stdlib;

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * DateTimeTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Stdlib\DateTime
 */
class DateTimeTest extends TestCase
{

    /**
     * Test default format
     */
    public function testDefaultFormat()
    {
        $date = new DateTime();
        $this->assertSame( DateTime::ISO8601, $date->getDefaultFormat() );

        $date->setDefaultFormat( 'rss' );
        $this->assertSame( DateTime::RSS, $date->getDefaultFormat() );

        $this->assertSame( $date->format( DateTime::RSS ), (string) $date );
    }

    /**
     * Test set-state
     */
    public function testSetState()
    {
        $native     = new \DateTime;
        $dateObject = DateTime::__set_state( (array) $native );

        $this->assertInstanceOf( 'Zork\Stdlib\DateTime', $dateObject );
        $this->assertSame( $native->format( \DateTime::ISO8601 ), $dateObject->format( DateTime::ISO8601 ) );
    }

    /**
     * Test create from format
     */
    public function testCreateFromFormat()
    {
        $dateString = date( DATE_ISO8601 );
        $dateObject = DateTime::createFromFormat( DateTime::ISO8601, $dateString );

        $this->assertInstanceOf( 'Zork\Stdlib\DateTime', $dateObject );
        $this->assertSame( $dateString, $dateObject->format( DateTime::ISO8601 ) );
    }

    /**
     * Helper for min/max search
     *
     * @return  array
     */
    protected function dates()
    {
        return array(
            new \DateTime(),
            null,
            strtotime( '+1 day' ),
            strtotime( '-1 day' ),
        );
    }

    /**
     * Test find min
     */
    public function testFindMin()
    {
        $dates = $this->dates();
        $min   = ( new \DateTime( '@' . strtotime( '-1 day' ) ) )->format( 'Y-m-d' );

        $this->assertEquals( $min, DateTime::min( $dates )->format( 'Y-m-d' ) );
        $this->assertEquals( $min, DateTime::min( new ArrayIterator( $dates ) )->format( 'Y-m-d' ) );
        $this->assertEquals( $min, call_user_func_array( 'Zork\Stdlib\DateTime::min', $dates )->format( 'Y-m-d' ) );
    }

    /**
     * Test find max
     */
    public function testFindMax()
    {
        $dates = $this->dates();
        $max   = ( new \DateTime( '@' . strtotime( '+1 day' ) ) )->format( 'Y-m-d' );

        $this->assertEquals( $max, DateTime::max( $dates )->format( 'Y-m-d' ) );
        $this->assertEquals( $max, DateTime::max( new ArrayIterator( $dates ) )->format( 'Y-m-d' ) );
        $this->assertEquals( $max, call_user_func_array( 'Zork\Stdlib\DateTime::max', $dates )->format( 'Y-m-d' ) );
    }

}
