<?php

namespace Zork\Session\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Service\SessionManagerFactory as ZendSessionManagerFactory;

/**
 * SessionConfigFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SessionManagerFactory extends ZendSessionManagerFactory
{

    /**
     * {@inheritDoc}
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        $manager = parent::createService( $serviceLocator );
        $storage = $manager->getStorage();

        if ( ! $storage->isImmutable() && ! $storage->getMetadata( '_STRICT' ) )
        {
            $manager->regenerateId( false );
            $storage->setMetadata( '_STRICT', true );
        }

        return $manager;
    }

}
