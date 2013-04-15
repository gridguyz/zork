<?php

namespace Zork\Db\Sql;

use Zend\Db\Sql\TableIdentifier as ZendTableIdentifier;


/**
 * TableIdentifier
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TableIdentifier extends ZendTableIdentifier
{

    /**
     * Get identifier chain
     *
     * @return array
     */
    public function getIdentifierChain()
    {
        $chain = array();

        if ( ! empty( $this->schema ) )
        {
            $chain[] = $this->schema;
        }

        $chain[] = $this->table;
        return $chain;
    }

}
