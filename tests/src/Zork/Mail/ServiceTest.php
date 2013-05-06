<?php

namespace Zork\Mail;

use Zork\Db\SiteInfo;
use Zend\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * ServiceTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Mail\Service
 * @covers Zork\Mail\ServiceFactory
 * @covers Zork\Mail\Transport\ServiceFactory
 */
class ServiceTest extends TestCase
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
    );

    /**
     * @var array
     */
    protected $dataSiteInfo = array(
        'domain' => 'domain.test',
    );

    /**
     * @var Zend\ServiceManager\ServiceManager
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
             ->setFactory( 'Zork\Mail\Service', 'Zork\Mail\ServiceFactory' );
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
        $service = $this->serviceManager->get( 'Zork\Mail\Service' );
        $this->assertInstanceOf( 'Zork\Mail\Service', $service );
        $this->assertInstanceOf( 'Zork\Mail\Transport\Callback', $service->getTransport() );

        $this->assertEquals(
            array(
                'email' => 'no-reply@domain.test',
                'name'  => 'domain.test',
            ),
            $service->getDefaultFrom()
        );

        $this->assertEquals(
            array(
                'email' => 'no-reply@domain.test',
                'name'  => 'domain.test',
            ),
            $service->getDefaultReplyTo()
        );
    }

    /**
     * Test setters
     */
    public function testSetters()
    {
        $service = $this->serviceManager->get( 'Zork\Mail\Service' );
        $service->setDefaultFrom( 'name@example.com', 'name' );
        $service->setDefaultReplyTo( 'name@example.com', 'name' );

        $this->assertEquals(
            array(
                'email' => 'name@example.com',
                'name'  => 'name',
            ),
            $service->getDefaultFrom()
        );

        $this->assertEquals(
            array(
                'email' => 'name@example.com',
                'name'  => 'name',
            ),
            $service->getDefaultReplyTo()
        );

        $service->setDefaultFrom( array() );
        $service->setDefaultReplyTo( array() );

        $this->assertEquals(
            array(
                'email' => null,
                'name'  => null,
            ),
            $service->getDefaultFrom()
        );

        $this->assertEquals(
            array(
                'email' => null,
                'name'  => null,
            ),
            $service->getDefaultReplyTo()
        );
    }

    /**
     * Test create message
     */
    public function testCreateMessage()
    {
        $service = $this->serviceManager->get( 'Zork\Mail\Service' );

        $message = $service->createMessage( new Message() );

        $this->assertArrayHasKey(
            'no-reply@domain.test',
            iterator_to_array( $message->getFrom() )
        );

        $this->assertArrayHasKey(
            'no-reply@domain.test',
            iterator_to_array( $message->getReplyTo() )
        );

        $message = $service->createMessage( array(
            'subject'   => 'foo',
            'X-Subject' => 'bar',
        ) );

        $this->assertEquals(
            'foo',
            $message->getSubject()
        );

        $headers = $message->getHeaders();
        $this->assertTrue( $headers->has( 'x-subject' ) );

        $this->assertEquals(
            'bar',
            $headers->get( 'x-subject' )
                    ->getFieldValue()
        );
    }

    /**
     * Test send
     */
    public function testSend()
    {
        $service = $this->serviceManager->get( 'Zork\Mail\Service' );
        $service->send( array(
            'to'    => 'name@example.com',
            'body'  => 'Example message',
        ) );

        $this->assertSame( 1, static::$sent );
    }

    /**
     * Test transport service
     */
    public function testTransportService()
    {
        $this->serviceManager
             ->setFactory(
                    'Zork\Mail\TransportService',
                    'Zork\Mail\Transport\ServiceFactory'
                );

        $this->assertSame(
            $this->serviceManager->get( 'Zork\Mail\Service' )->getTransport(),
            $this->serviceManager->get( 'Zork\Mail\TransportService' )
        );
    }

}
