<?php

namespace Zork\Stdlib;

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * DateTimeTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
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
        $min   = date( 'Y-m-d', strtotime( '-1 day' ) );

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
        $max   = date( 'Y-m-d', strtotime( '+1 day' ) );

        $this->assertEquals( $max, DateTime::max( $dates )->format( 'Y-m-d' ) );
        $this->assertEquals( $max, DateTime::max( new ArrayIterator( $dates ) )->format( 'Y-m-d' ) );
        $this->assertEquals( $max, call_user_func_array( 'Zork\Stdlib\DateTime::max', $dates )->format( 'Y-m-d' ) );
    }

}
