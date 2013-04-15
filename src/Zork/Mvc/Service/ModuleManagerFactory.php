<?php

namespace Zork\Mvc\Service;

use Zork\Db\Config\LoaderListener;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Service\ModuleManagerFactory as ZendModuleManagerFactory;

/**
 * EventManagerFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ModuleManagerFactory extends ZendModuleManagerFactory
{

    /**
     * Creates and returns the module manager
     *
     * Instantiates the default module listeners, providing them configuration
     * from the "module_listener_options" key of the ApplicationConfig
     * service. Also sets the default config glob path.
     *
     * Module manager is instantiated and provided with an EventManager, to which
     * the default listener aggregate is attached. The ModuleEvent is also created
     * and attached to the module manager.
     *
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zend\ModuleManager\ModuleManager
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        $moduleManager  = parent::createService( $serviceLocator );
        $modules        = $moduleManager->getModules();
        $db             = $serviceLocator->get( 'Zend\Db\Adapter\Adapter' );
        $platform       = $db->getPlatform();
        $query          = $db->query( '
            SELECT ' . $platform->quoteIdentifier( 'module' ) . '
              FROM ' . $platform->quoteIdentifier( 'module' ) . '
             WHERE ' . $platform->quoteIdentifier( 'enabled' ) . '
        ' );

        foreach ( $query->execute() as $row )
        {
            $modules[] = $row['module'];
        }

        $moduleManager->getEventManager()
                      ->attach( new LoaderListener( $db ), -999 );

        return $moduleManager->setModules( $modules );
    }

}
