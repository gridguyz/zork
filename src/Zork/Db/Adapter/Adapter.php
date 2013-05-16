<?php

namespace Zork\Db\Adapter;

use Zend\Db\Adapter\Driver\Pdo\Pdo;
use Zend\Db\Adapter\Adapter as ZendAdapter;

/**
 * Adapter
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Adapter extends ZendAdapter
{

    /**
     * {@inheritDoc}
     */
    protected function createDriver( $parameters )
    {
        if ( is_array( $parameters ) &&
             isset( $parameters['driver'] ) &&
             isset( $parameters['pdodriver'] ) &&
             is_string( $parameters['driver'] ) &&
             is_string( $parameters['pdodriver'] ) &&
             strtolower( $parameters['driver'] ) == 'pdo' &&
             strtolower( $parameters['pdodriver'] ) == 'pgsql' )
        {
            return new Pdo(
                new Driver\Pdo\PgsqlConnection( $parameters ),
                new Driver\Pdo\Statement()
            );
        }

        return parent::createDriver( $parameters );
    }

}
