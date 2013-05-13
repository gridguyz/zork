<?php

namespace Zork\Log;

use Zend\Log\Logger;
use Zork\Db\SiteInfo;
use Zork\Test\PHPUnit\TestCase;
use Zend\ServiceManager\ServiceManager;
use PHPUnit_Framework_Constraint_PCREMatch as PCREMatch;

/**
 * LoggerManagerTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Log\LoggerManager
 * @covers Zork\Log\LoggerServiceFactory
 * @covers Zork\Log\Formatter\ExtraHandler
 * @covers Zork\Log\Processor\Environment
 * @covers Zork\Log\Writer\FormattedMock
 */
class LoggerManagerTest extends TestCase
{

    /**
     * @var array
     */
    protected $dataConfig = array(
        'mail' => array(
            'transport' => array(
                'type'      => 'Zork\Mail\Transport\Callback',
                'options'   => array(
                    'type'      => 'Zork\Mail\Transport\CallbackOptions',
                    'options'   => array(
                        'callback'  => array( __CLASS__, 'sendHandler' ),
                    ),
                ),
            ),
        ),
        'log' => array(
            'null'  => array(
                'writers'   => array(
                    'null'      => array(
                        'name'  => 'null',
                    ),
                ),
            ),
            'sample'    => array(
                'writers'   => array(
                    'mail'      => array(
                        'name'  => 'mail',
                        'filters'   => array(
                            'priority'  => array(
                                'name'      => 'priority',
                                'options'   => array(
                                    'priority'  => Logger::ERR,
                                    'operator'  => '>=',
                                ),
                            ),
                        ),
                    ),
                    'mock'      => array(
                        'name'  => 'mock',
                        'filters'   => array(
                            'mock'  => array(
                                'name'  => 'mock',
                            ),
                        ),
                    ),
                    'formatted' => array(
                        'name'  => 'Zork\Log\Writer\FormattedMock',
                        'formatter' => array(
                            'name'  => 'Zork\Log\Formatter\ExtraHandler',
                        ),
                    ),
                ),
                'processors'    => array(
                    'env'       => array(
                        'name'  => 'Zork\Log\Processor\Environment',
                    ),
                ),
            ),
        ),
    );

    /**
     * @var array
     */
    protected $dataSiteInfo = array(
        'domain' => 'domain.test',
    );

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var int
     */
    protected static $sent = 0;

    /**
     * Send-mail handler
     *
     * @param mixed $message
     * @param mixed $transport
     */
    public static function sendHandler( $message, $transport )
    {
        static::$sent++;
        static::assertInstanceOf( 'Zend\Mail\Message', $message );
        static::assertInstanceOf( 'Zork\Mail\Transport\Callback', $transport );
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        static::$sent = 0;
        $this->serviceManager = new ServiceManager();

        $this->serviceManager
             ->setService( 'Configuration', $this->dataConfig )
             ->setService( 'SiteInfo', new SiteInfo( $this->dataSiteInfo ) )
             ->setAlias( 'Zork\Db\SiteInfo', 'SiteInfo' )
             ->setFactory( 'Zork\Mail\Service', 'Zork\Mail\ServiceFactory' )
             ->setFactory( 'Zork\Log\LoggerManager', 'Zork\Log\LoggerServiceFactory' );
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();
        static::$sent = 0;
        $this->serviceManager = null;
    }

    /**
     * Test service factory
     */
    public function testServiceFactory()
    {
        $manager = $this->serviceManager->get( 'Zork\Log\LoggerManager' );
        $this->assertInstanceOf( 'Zork\Log\LoggerManager', $manager );
        $this->assertTrue( $manager->hasLogger( 'null' ) );
        $this->assertInstanceOf( 'Zend\Log\Logger', $manager->getLogger( 'null' ) );
        $this->assertFalse( $manager->hasLogger( 'non-existing' ) );
        $this->setExpectedException( 'InvalidArgumentException' );
        $manager->getLogger( 'non-existing' );
    }

    /**
     * Test logger
     */
    public function testLogger()
    {
        /* @var $logger Logger */
        $manager = $this->serviceManager->get( 'Zork\Log\LoggerManager' );
        $this->assertTrue( $manager->hasLogger( 'sample' ) );
        $logger = $manager->getLogger( 'sample' );

        $processors = $logger->getProcessors()->toArray();
        $this->assertCount( 1, $processors );

        foreach ( $processors as $processor )
        {
            $this->assertInstanceOf( 'Zork\Log\Processor\Environment', $processor );
        }

        $writers = $logger->getWriters()->toArray();
        $this->assertCount( 3, $writers );
        $writerClasses = array();

        foreach ( $writers as $writer )
        {
            $class = get_class( $writer );

            if ( empty( $writerClasses[$class] ) )
            {
                $writerClasses[$class] = 0;
            }

            $writerClasses[$class]++;
        }

        $this->assertEquals(
            array(
                'Zend\Log\Writer\Mail'          => 1,
                'Zend\Log\Writer\Mock'          => 1,
                'Zork\Log\Writer\FormattedMock' => 1,
            ),
            $writerClasses
        );

        $logger->log( Logger::EMERG,    'EMERG'     ); // mail sent
        $logger->log( Logger::ALERT,    'ALERT'     ); // mail sent
        $logger->log( Logger::CRIT,     'CRIT'      ); // mail sent
        $logger->log( Logger::ERR,      'ERR'       ); // mail sent
        $logger->log( Logger::WARN,     'WARN'      );
        $logger->log( Logger::NOTICE,   'NOTICE'    );
        $logger->log( Logger::INFO,     'INFO'      );
        $logger->log( Logger::DEBUG,    'DEBUG'     );

        foreach ( $writers as $writer )
        {
            $writer->shutdown();

            switch ( get_class( $writer ) )
            {
                case 'Zend\Log\Writer\Mock':
                    /* @var $writer \Zend\Log\Writer\Mock */
                    $this->assertTrue( $writer->shutdown );
                    $this->assertCount( 8, $writer->events );

                    $this->assertEvery(
                        function ( $event ) {
                            return is_array( $event )
                                && isset( $event['message'] )
                                && isset( $event['priority'] )
                                && defined( $const = 'Zend\Log\Logger::' . $event['message'] )
                                && $event['priority'] == constant( $const );
                        },
                        $writer->events
                    );

                    break;

                case 'Zork\Log\Writer\FormattedMock':
                    /* @var $writer \Zork\Log\Writer\FormattedMock */
                    $this->assertTrue( $writer->shutdown );
                    $this->assertCount( 8, $writer->messages );

                    $this->assertEvery(
                        new PCREMatch( '/extra/i' ),
                        $writer->messages
                    );

                    break;
            }
        }

        $logger     = null;
        $writers    = null;
        $processors = null;

        $this->assertGreaterThan( 0, static::$sent );
        $this->assertLessThanOrEqual( 4, static::$sent );
    }

}
