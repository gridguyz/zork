<?php

namespace Zork\Db\Sql\Expression;

use Zend\Db\Sql\Expression;

/**
 * FunctionCall
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FunctionCall extends Expression
{

    /**
     * @param string|null $name
     * @param array $arguments
     * @param array $types
     * @return array
     */
    protected function getExpressionParametersTypes( $name,
                                                     array $arguments,
                                                     array $types )
    {
        $expression = '';
        $parameters = null;

        if ( $name )
        {
            $parameters = array_values( $arguments );
            $types      = array_values( $types );
            array_unshift( $parameters, $name );
            array_unshift( $types, self::TYPE_IDENTIFIER );

            if ( empty( $arguments ) )
            {
                $expression = '?()';
            }
            else
            {
                $expression = '?(' .
                    implode( ', ', array_fill( 0, count( $arguments ), '?' ) ) .
                ')';
            }
        }

        return array( $expression, $parameters, $types );
    }

    /**
     * Function call expression
     *
     * @param string $name
     * @param array $arguments
     * @param array $types
     */
    public function __construct( $name = null,
                                 array $arguments = array(),
                                 array $types = array() )
    {
        list( $expression,
              $parameters,
              $types ) = $this->getExpressionParametersTypes( $name,
                                                              $arguments,
                                                              $types );

        parent::__construct( $expression, $parameters, $types );
    }

    /**
     *
     * @param string|null $name
     * @param array $arguments
     * @param array $types
     * @return FunctionCall
     */
    public function call( $name = null,
                          array $arguments = array(),
                          array $types = array() )
    {
        list( $expression,
              $parameters,
              $types ) = $this->getExpressionParametersTypes( $name,
                                                              $arguments,
                                                              $types );

        return $this->setExpression( $expression )
                    ->setParameters( $parameters )
                    ->setTypes( $types );
    }

}
