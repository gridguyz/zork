<?php

namespace Zork\I18n\View\Helper;

use DateTime;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * RelativeTimeTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class RelativeTimeTest extends TestCase
{

    /**
     * Test constructor & "from" getter & setter
     */
    public function testConstructorAndFromGetterAndSetter()
    {
        $helper = new RelativeTime;

        $this->assertInstanceOf( 'DateTime', $helper->getFrom() );

        $now    = new DateTime( 'now' );
        $unix   = 1388534400;
        $iso    = '2014-01-01T01:01:01+0000';

        $helper->setFrom( $now );

        $this->assertEquals(
            $now->format( DateTime::ISO8601 ),
            $helper->getFrom()
                   ->format( DateTime::ISO8601 )
        );

        $helper->setFrom( $unix );

        $this->assertEquals(
            $unix,
            $helper->getFrom()
                   ->format( 'U' )
        );

        $helper->setFrom( $iso );

        $this->assertEquals(
            $iso,
            $helper->getFrom()
                   ->format( DateTime::ISO8601 )
        );
    }

    /**
     * Test invoke without arguments
     */
    public function testInvokeWithoutArguments()
    {
        $helper = new RelativeTime;
        $this->assertInstanceOf( __NAMESPACE__ . '\RelativeTime', $helper() );
    }

    /**
     * Test invoke
     */
    public function testInvoke()
    {
        $view = $this->getMock( 'Zend\View\Renderer\PhpRenderer' );

        $view->expects( $this->any() )
             ->method( '__call' )
             ->will( $this->returnCallback( function ( $name, $args ) {
                 return $name == 'translate' ? reset( $args ) : null;
             } ) );

        $helper = new RelativeTime( '2014-02-02T02:02:02Z' );
        $helper->setView( $view );

        $this->assertEquals( 'default.justNow',             $helper( '2014-02-02T02:02:01Z' ) );
        $this->assertEquals( 'default.justNow',             $helper( '2014-02-02T02:02:02Z' ) );
        $this->assertEquals( 'default.justNow',             $helper( '2014-02-02T02:02:03Z' ) );

        $this->assertEquals( 'default.seconds.60.ago',      $helper( '2014-02-02T02:01:02Z' ) );
        $this->assertEquals( 'default.seconds.60.fromNow',  $helper( '2014-02-02T02:03:02Z' ) );

        $this->assertEquals( 'default.minutes.60.ago',      $helper( '2014-02-02T01:02:02Z' ) );
        $this->assertEquals( 'default.minutes.60.fromNow',  $helper( '2014-02-02T03:02:02Z' ) );

        $this->assertEquals( 'default.hours.24.ago',        $helper( '2014-02-01T02:02:02Z' ) );
        $this->assertEquals( 'default.hours.24.fromNow',    $helper( '2014-02-03T02:02:02Z' ) );

        $this->assertEquals( 'default.days.3.ago',          $helper( '2014-01-30T02:02:02Z' ) );
        $this->assertEquals( 'default.days.3.fromNow',      $helper( '2014-02-05T02:02:02Z' ) );

        $this->assertEquals( 'default.days.30.ago',         $helper( '2014-01-03T02:02:02Z' ) );
        $this->assertEquals( 'default.days.30.fromNow',     $helper( '2014-03-04T02:02:02Z' ) );

        $this->assertEquals( 'default.months.12.ago',       $helper( '2013-02-02T02:02:02Z' ) );
        $this->assertEquals( 'default.months.12.fromNow',   $helper( '2015-02-02T02:02:02Z' ) );

        $this->assertEquals( 'default.years.10.ago',        $helper( '2004-02-02T02:02:02Z' ) );
        $this->assertEquals( 'default.years.10.fromNow',    $helper( '2024-02-02T02:02:02Z' ) );

        $helper->setFrom( 1388534400 );
        $this->assertEquals( 'default.justNow', $helper( 1388534400 ) );

        $now = new DateTime;
        $helper->setFrom( $now );
        $this->assertEquals( 'default.justNow', $helper( $now ) );
    }

}
