<?php

namespace Zork\Session\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Service\SessionConfigFactory as ZendSessionConfigFactory;

/**
 * SessionConfigFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SessionConfigFactory extends ZendSessionConfigFactory
{

    /**
     * {@inheritDoc}
     */
    public function createService( ServiceLocatorInterface $services )
    {
        /* @var $siteInfo \Zork\Db\SiteInfo */
        $siteInfo       = $services->get( 'SiteInfo' );
        $sessionConfig  = parent::createService( $services );
        $sessionConfig->setCookieDomain( '.' . $siteInfo->getDomain() );
        return $sessionConfig;
    }

}
