<?php

namespace Zork\Rpc;

use ReflectionObject;
use ReflectionException;
use Zend\Stdlib\ArrayUtils;

/**
 * InvokableTrait
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @method mixed __invoke( $... ) {@abstract}
 */
trait CallableTrait
{

    /**
     * Invoke the rpc-function with params
     *
     * @param string $name
     * @param array|\Traversable $params
     * @return mixed
     */
    public function call( $name, $params )
    {
        if ( ! empty( static::$notCallableMethods ) &&
             in_array( $name, static::$notCallableMethods ) )
        {
            throw new Exception\BadMethodCallException(
                get_called_class() . '::' . $name .
                '() is not callable from rpc'
            );
        }

        if ( ! empty( static::$onlyCallableMethods ) &&
             ! in_array( $name, static::$onlyCallableMethods ) )
        {
            throw new Exception\BadMethodCallException(
                get_called_class() . '::' . $name .
                '() is not callable from rpc'
            );
        }

        $args   = array();
        $params = ArrayUtils::iteratorToArray( $params );
        $refObj = new ReflectionObject( $this );

        try
        {
            $refMet = $refObj->getMethod( $name );
        }
        catch ( ReflectionException $ex )
        {
            throw new Exception\BadMethodCallException(
                get_called_class() . '::' . $name .
                '() is not a valid method',
                0, $ex
            );
        }

        if ( empty( $refMet ) )
        {
            // @codeCoverageIgnoreStart
            throw new Exception\BadMethodCallException(
                get_called_class() . '::' . $name .
                '() is not a valid method'
            );
            // @codeCoverageIgnoreEnd
        }
        else
        {
            foreach ( $params as $index => $argument )
            {
                if ( is_numeric( $index ) )
                {
                    $args[$index] = $argument;
                    unset( $params[$index] );
                }
            }

            foreach ( $refMet->getParameters() as $param )
            {
                /* @var $param \ReflectionParameter */
                $paramName  = $param->getName();
                $index      = $param->getPosition();

                switch ( true )
                {
                    case isset( $args[$index] ):
                        if ( isset( $params[$paramName] ) )
                        {
                            throw new Exception\InvalidArgumentException(
                                'Parameter "' . $paramName .
                                '" added by name & index too'
                            );
                        }
                        break;

                    case isset( $params[$paramName] ):
                        $args[$index] = $params[$paramName];
                        unset( $params[$paramName] );
                        break;

                    case $param->isDefaultValueAvailable():
                        $args[$index] = $param->getDefaultValue();
                        break;

                    case $param->isOptional() && $param->allowsNull():
                        $args[$index] = null;
                        break;

                    default:
                        throw new Exception\InvalidArgumentException(
                            'Parameter "' . $paramName .
                            '" is required'
                        );
                }
            }

            ksort( $args );
        }

        return $refMet->invokeArgs( $this, $args );
    }

}
