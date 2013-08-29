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
     * @param   string|array    $name
     * @param   array           $args
     * @param   string          $mode
     * @return  \Zork\Db\Sql\FunctionCall
     */
    public function functionCall( $name = null, array $args = null, $mode = null )
    {
        return new FunctionCall( $name, $args, $mode );
    }

    /**
     * Call an sql-function & return it's result
     *
     * @param   string|array    $name quoted with $platform->quoteIdentifierChain()
     * @param   array           $args
     * @param   string          $mode
     * @return  mixed
     */
    public function call( $name, array $args = null, $mode = null )
    {
        if ( is_string( $name ) &&
             $this->table instanceof ZendTableIdentifier &&
             $this->table->hasSchema() )
        {
            $name = array( $this->table->getSchema(), $name );
        }

        $functionCall = new FunctionCall( $name, $args, $mode );

        /* @var $result \Zend\Db\Adapter\Driver\ResultInterface */

        $result = $this->prepareStatementForSqlObject( $functionCall )
                       ->execute();
        $mode   = $functionCall->getMode();

        if ( FunctionCall::MODE_RESULT_SET == $mode )
        {
            return $result;
        }

        if ( FunctionCall::MODE_SINGLE == $mode )
        {
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

        throw new Exception\InvalidArgumentException( sprintf(
            '%s: mode "%s" not suported',
            __METHOD__,
            $mode
        ) );
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