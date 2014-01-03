<?php

namespace Zork\Session\Service;

use Zend\Session\Storage\ArrayStorage;
use Zend\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * SessionManagerFactoryTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SessionManagerFactoryTest extends TestCase
{

    /**
     * Test get SessionManager
     *
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testCreateService()
    {
        $serviceManager = new ServiceManager;
        $serviceFactory = new SessionManagerFactory;

        $serviceManager->setAlias( 'Configuration', 'Config' )
                       ->setService( 'Config', array() )
                       ->setService(
                            'Zend\Session\Storage\StorageInterface',
                            new ArrayStorage
                        );

        $serviceFactory->createService( $serviceManager );
    }

}
