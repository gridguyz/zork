<?php

namespace Zork\Http\Client;

use Zend\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * ServiceFactoryTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Http\Client\ServiceFactory
 * @covers Zork\Http\Client\Adapter\ServiceFactory
 */
class ServiceFactoryTest extends TestCase
{

    /**
     * @var array
     */
    protected $dataConfig = array(
        'http'      => array(
            'adapter'   => 'Zend\Http\Client\Adapter\Socket',
            'options'   => array(),
        ),
    );

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->serviceManager = new ServiceManager;

        $this->serviceManager
             ->setService( 'Configuration', $this->dataConfig )
             ->setFactory( 'Zend\Http\Client\Adapter\AdapterInterface',
                           'Zork\Http\Client\Adapter\ServiceFactory' )
             ->setAlias( 'Zend\Http\Client\Adapter',
                         'Zend\Http\Client\Adapter\AdapterInterface' )
             ->setFactory( 'Zend\Http\Client',
                           'Zork\Http\Client\ServiceFactory' )
             ->setShared( 'Zend\Http\Client', false );
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();
        $this->serviceManager = null;
    }

    /**
     * Test service factories
     */
    public function testServiceFactories()
    {
        $this->assertInstanceof(
            'Zend\Http\Client\Adapter\AdapterInterface',
            $this->serviceManager->get( 'Zend\Http\Client\Adapter' )
        );

        $this->assertInstanceof(
            'Zend\Http\Client',
            $this->serviceManager->get( 'Zend\Http\Client' )
        );
    }

}
