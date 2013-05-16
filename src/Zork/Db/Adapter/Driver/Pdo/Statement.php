<?php

namespace Zork\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\Exception\InvalidQueryException;
use Zend\Db\Adapter\Driver\Pdo\Statement as ZendStatement;

/**
 * Statement
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Statement extends ZendStatement
{

    /**
     * Execute the statement
     *
     * @param   mixed $parameters
     * @return  \Zend\Db\Adapter\Driver\Pdo\Result
     * @throws  InvalidQueryException
     */
    public function execute( $parameters = null )
    {
        try
        {
            return parent::execute( $parameters );
        }
        catch ( InvalidQueryException $exception )
        {
            throw new InvalidQueryException(
                $exception->getMessage() . ':' .
                PHP_EOL . $this->resource->queryString,
                $exception->getCode(),
                $exception
            );
        }
    }

}
