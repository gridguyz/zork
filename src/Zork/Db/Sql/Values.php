<?php

namespace Zork\Db\Sql;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\ExpressionInterface;

/**
 * FunctionCall
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Values extends Select
{

    /**
     * @var array Specification array
     */
    protected $specifications = array(
        self::SELECT => array(
            'SELECT %1$s' => array(
                array(1 => '%1$s', 2 => '%1$s AS %2$s', 'combinedby' => ', '),
                null
            ),
            'SELECT %1$s %2$s' => array(
                null,
                array(1 => '%1$s', 2 => '%1$s AS %2$s', 'combinedby' => ', '),
                null
            ),
        ),
    );

    /**
     * Constructor
     *
     * @param   array|null  $values
     */
    public function __construct( array $values = null )
    {
        parent::__construct( 'dual' );

        if ( null !== $values )
        {
            $this->values( $values );
        }
    }

    /**
     * Specify columns from which to select
     *
     * Possible valid states:
     *
     *   array(value, ...)
     *     value can be strings or Expression objects
     *
     *   array(string => value, ...)
     *     key string will be use as alias,
     *     value can be string or Expression objects
     *
     * @param   array   $columns
     * @return  Values
     */
    public function columns( array $columns )
    {
        return parent::columns( $columns, false );
    }

    /**
     * Specify values which to select
     *
     * Possible valid states:
     *
     *   array(value, ...)
     *     value can be scalars or Expression objects
     *
     *   array(string => value, ...)
     *     key string will be use as alias,
     *     value can be scalar or Expression objects
     *
     * @param   array   $values
     * @return  Values
     */
    public function values( array $values )
    {
        foreach ( $values as &$spec )
        {
            if ( ! $spec instanceof ExpressionInterface )
            {
                if ( null === $spec )
                {
                    $spec = new Expression( 'NULL' );
                }
                else
                {
                    $spec = new Expression(
                        '?',
                        array( (string) $spec ),
                        array( Expression::TYPE_VALUE )
                    );
                }
            }
        }

        return parent::columns( $values, false );
    }

}
