<?php

namespace Zork\Db\Sql\Expression;

use Zend\Db\Sql\Expression;

/**
 * ArrayLiteral
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ArrayLiteral extends Expression
{

    /**
     * @param array $values
     * @param array $types
     * @return array
     */
    protected function getExpressionParametersTypes( array $values,
                                                     array $types )
    {
        $expression = '';
        $parameters = array_values( $values );
        $types      = array_values( $types );

        while ( count( $types ) < count( $parameters ) )
        {
            $types[] = self::TYPE_VALUE;
        }

        if ( empty( $values ) )
        {
            $expression = 'ARRAY[]';
        }
        else
        {
            $expression = 'ARRAY[' .
                implode( ', ', array_fill( 0, count( $values ), self::PLACEHOLDER ) ) .
            ']';
        }

        return array( $expression, $parameters, $types );
    }

    /**
     * Array literal expression
     *
     * @param   array   $values
     * @param   array   $types
     */
    public function __construct( array $values  = array(),
                                 array $types   = array() )
    {
        list( $expression,
              $parameters,
              $types ) = $this->getExpressionParametersTypes( $values,
                                                              $types );

        parent::__construct( $expression, $parameters, $types );
    }

    /**
     * @param   array   $arguments
     * @param   array   $types
     * @return  ArrayLiteral
     */
    public function setValues( array $values    = array(),
                               array $types     = array() )
    {
        list( $expression,
              $parameters,
              $types ) = $this->getExpressionParametersTypes( $values,
                                                              $types );

        return $this->setExpression( $expression )
                    ->setParameters( $parameters )
                    ->setTypes( $types );
    }

}
