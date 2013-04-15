<?php

namespace Zork\Db\Sql;

use Zend\Db\Sql\Sql as ZendSql;
use Zend\Db\Sql\TableIdentifier as ZendTableIdentifier;

/**
 * Sql
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Sql extends ZendSql
{

    /**
     * @param string|array $name
     * @return \Zork\Db\Sql\FunctionCall
     */
    public function functionCall( $name = null )
    {
        return new FunctionCall( $name );
    }

    /**
     * Call an sql-function & return it's result
     *
     * @param string|array $name quoted with $platform->quoteIdentifierChain()
     * @param array $args
     * @return mixed
     */
    public function call( $name, array $args = array() )
    {
        if ( is_string( $name ) &&
             $this->table instanceof ZendTableIdentifier &&
             $this->table->hasSchema() )
        {
            $name = array( $this->table->getSchema(), $name );
        }

        $functionCall = new FunctionCall( $name );
        $functionCall->arguments( $args );

        /* @var $result \Zend\Db\Adapter\Driver\ResultInterface */

        $result = $this->prepareStatementForSqlObject( $functionCall )
                       ->execute();

        if ( empty( $result ) )
        {
            return null;
        }

        $row = $result->current();

        if ( empty( $row ) )
        {
            return null;
        }

        $resultKey = $functionCall->getResultKey();

        if ( isset( $row[$resultKey] ) )
        {
            return $row[$resultKey];
        }

        return null;
    }

    /**
     * Magic call an sql-function
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call( $name, array $args )
    {
        $name = (string) $name;

        if ( false !== strpos( $name, '.' ) )
        {
            $name = explode( '.', $name );
        }

        return $this->call( $name, $args );
    }

}