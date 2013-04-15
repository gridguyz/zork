<?php

namespace Zork\Db\Sql\Predicate;

use Zend\Db\Sql\Predicate\In;

/**
 * ExpressionIn
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ExpressionIn extends In
{

    protected $selectSpecification = ' IN %s';
    protected $valueSpecSpecification = ' IN (%s)';

    /**
     * Constructor
     *
     * @param   string      $specification
     * @param   null|string $identifier
     * @param   array       $valueSet
     */
    public function __construct( $specification, $identifier = null, $valueSet = null )
    {
        $this->selectSpecification    = preg_replace( '/\\?/', '%s',  $specification, 1 ) . $this->selectSpecification;
        $this->valueSpecSpecification = preg_replace( '/\\?/', '%%s', $specification, 1 ) . $this->valueSpecSpecification;
        parent::__construct( $identifier, $valueSet );
    }

}
