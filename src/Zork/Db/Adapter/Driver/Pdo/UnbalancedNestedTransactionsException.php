<?php

namespace Zork\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\Exception\InvalidQueryException;
use Zork\Db\Exception\ExceptionInterface;

/**
 * UnbalancedNestedTransactionsException
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class UnbalancedNestedTransactionsException extends InvalidQueryException
                                         implements ExceptionInterface
{

}
