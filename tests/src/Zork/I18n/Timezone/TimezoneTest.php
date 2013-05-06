<?php

namespace Zork\I18n\Timezone;

use ArrayIterator;
use Zend\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * TimezoneTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\I18n\Timezone\Timezone
 * @covers Zork\I18n\Timezone\TimezoneServiceFactory
 */
class TimezoneTest extends TestCase
{

    /**
     * @var string
     */
    private $previousTimezone;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $this->previousTimezone = date_default_timezone_get();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();

        if ( null !== $this->previousTimezone )
        {
            date_default_timezone_set( $this->previousTimezone );
        }

        $this->previousTimezone = null;
    }

    /**
     * Test service factory
     */
    public function testServiceFactory()
    {
        $service = new ServiceManager();
        $config  = array(
            'timezone' => array(
                'id'   => 'Europe/London',
            ),
        );

        $service->setService( 'Configuration', $config )
                ->setFactory( 'Zork\I18n\Timezone\Timezone',
                              'Zork\I18n\Timezone\TimezoneServiceFactory' )
                ->setAlias( 'Timezone', 'Zork\I18n\Timezone\Timezone' );

        /* @var $zone Timezone */
        $zone = $service->get( 'Timezone' );

        $this->assertInstanceOf( 'Zork\I18n\Timezone\Timezone', $zone );
        $this->assertEquals( 'Europe/London', $zone->getCurrent() );
        $this->assertEquals( 'Europe/London', (string) $zone );
    }

    /**
     * Test factory method
     */
    public function testFactoryMethod()
    {
        $zone = Timezone::Factory( new ArrayIterator( array(
            'id'    => 'Europe/London',
        ) ) );

        $this->assertSame( 'Europe/London', $zone->getCurrent() );

        $zone->setCurrent( 'UTC' );
        $this->assertEquals( 'UTC', $zone->getCurrent() );
    }

    /**
     * Test factory method called with non-traversable
     *
     * @expectedException   InvalidArgumentException
     */
    public function testFactoryMethodNonTraversable()
    {
        Timezone::Factory( 0 );
    }

}
