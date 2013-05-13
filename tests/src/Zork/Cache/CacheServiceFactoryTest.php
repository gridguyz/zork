<?php

namespace Zork\Cache;

use Zend\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * CacheServiceFactoryTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Cache\CacheServiceFactory
 */
class CacheServiceFactoryTest extends TestCase
{

    /**
     * @var array
     */
    protected $dataConfig = array(
        'cache' => array(
            'storage' => array(
                'adapter' => array(
                    'name' => 'memory',
                ),
            ),
            'plugins' => array(
                'exception_handler' => array(
                    'throw_exceptions' => false,
                ),
            ),
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

        $this->serviceManager = new ServiceManager();

        $this->serviceManager
             ->setService( 'Configuration', $this->dataConfig )
             ->setFactory( 'Zork\Cache\CacheManager', 'Zork\Cache\CacheServiceFactory' );
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
     * Test service factory
     */
    public function testServiceFactory()
    {
        $manager = $this->serviceManager->get( 'Zork\Cache\CacheManager' );
        $this->assertInstanceOf( 'Zork\Cache\CacheManager', $manager );
        $this->assertInstanceOf( 'Zend\Cache\Storage\StorageInterface', $manager->getStorage() );
    }

}
