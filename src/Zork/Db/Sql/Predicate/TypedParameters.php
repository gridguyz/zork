<?php

namespace Zork\Db\Sql\Predicate;

use Zend\Db\Sql\Predicate\Expression;

/**
 * TypedParameters
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TypedParameters extends Expression
{

    /**
     * @param   string          $expression
     * @param   string|array    $parameters
     * @param   array           $types
     */
    public function __construct( $expression    = '',
                                 $parameters    = null,
                                 array $types   = array() )
    {
        \Zend\Db\Sql\Expression::__construct( $expression, $parameters, $types );
    }

}
