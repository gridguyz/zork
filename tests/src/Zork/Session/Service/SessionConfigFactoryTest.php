<?php

namespace Zork\Session\Service;

use Zork\Db\SiteInfo;
use Zend\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * SessionConfigFactoryTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SessionConfigFactoryTest extends TestCase
{

    /**
     * Test get SessionManager
     */
    public function testCreateService()
    {
        $serviceManager = new ServiceManager;
        $serviceFactory = new SessionConfigFactory;

        $serviceManager->setAlias( 'Zork\Db\SiteInfo', 'SiteInfo' )
                       ->setAlias( 'Configuration', 'Config' )
                       ->setService( 'Config', array( 'session_config' => array() ) )
                       ->setService(
                            'SiteInfo',
                            new SiteInfo( array( 'domain' => 'example.com' ) )
                        );

        /* @var $service \Zend\Session\Config\ConfigInterface */
        $service = $serviceFactory->createService( $serviceManager );

        $this->assertInstanceOf( 'Zend\Session\Config\ConfigInterface', $service );
        $this->assertEquals( '.example.com', $service->getCookieDomain() );
    }

}
