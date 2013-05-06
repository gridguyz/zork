<?php

namespace Zork\Log;

use Zork\Db\SiteInfo;
use Zend\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * LoggerManagerTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Log\LoggerManager
 * @covers Zork\Log\LoggerServiceFactory
 * @covers Zork\Log\Formatter\ExtraHandler
 * @covers Zork\Log\Processor\Environment
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
                    ),
                    'mock'      => array(
                        'name'  => 'mock',
                    ),
                    'formatted' => array(
                        'name'  => 'Zork\Log\Writer\FormattedMock',
                    ),
                ),
                'formatter'     => array(
                    'name'      => 'Zork\Log\Formatter\ExtraHandler',
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
    }

}
