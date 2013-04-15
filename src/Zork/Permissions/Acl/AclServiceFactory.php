<?php

namespace Zork\Permissions\Acl;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * AclServiceFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AclServiceFactory implements FactoryInterface
{

    /**
     * Create the form-service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zend\Permissions\Acl\Acl
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        // Configure the locale
        $config     = $serviceLocator->get( 'Configuration' );
        $srvConfig  = isset( $config['acl'] ) ? $config['acl'] : array();
        $acl        = new Acl();

        if ( ! empty( $srvConfig['roles'] ) )
        {
            foreach ( (array) $srvConfig['roles'] as $role => $parents )
            {
                $acl->addRole( (string) $role, $parents );
            }
        }

        if ( ! empty( $srvConfig['resources'] ) )
        {
            foreach ( (array) $srvConfig['resources'] as $resource => $parent )
            {
                $acl->addResource( $resource, $parent );
            }
        }

        if ( ! empty( $srvConfig['allow'] ) )
        {
            foreach ( (array) $srvConfig['allow'] as $allow )
            {
                $acl->allow( $allow['role'], $allow['resource'], $allow['privilege'] );
            }
        }

        if ( ! empty( $srvConfig['deny'] ) )
        {
            foreach ( (array) $srvConfig['deny'] as $deny )
            {
                $acl->deny( $deny['role'], $deny['resource'], $deny['privilege'] );
            }
        }

        return $acl;
    }

}
