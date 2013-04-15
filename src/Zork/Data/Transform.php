<?php

namespace Zork\Data;

use Traversable;
use ReflectionClass;
use Zork\Stdlib\DateTime;

/**
 * Transform
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Transform
{

    /**
     * @param   mixed   $value
     * @return  string
     */
    public static function toString( $value )
    {
        return (string) $value;
    }

    /**
     * @param   mixed   $value
     * @return  int
     */
    public static function toInteger( $value )
    {
        return (int) $value;
    }

    /**
     * @param   mixed   $value
     * @return  float
     */
    public static function toFloat( $value )
    {
        return (float) $value;
    }

    /**
     * @param   mixed   $value
     * @return  bool
     */
    public static function toBoolean( $value )
    {
        return (bool) $value;
    }

    /**
     * @param   mixed   $value
     * @return  array
     */
    public static function toArray( $value )
    {
        if ( $value instanceof Traversable )
        {
            $value = iterator_to_array( $value );
        }

        return (array) $value;
    }

    /**
     * @param   mixed   $value
     * @return  callable
     * @throws  Exception\InvalidArgumentException
     */
    public static function toCallable( $value )
    {
        if ( is_callable( $value ) )
        {
            return $value;
        }

        if ( is_scalar( $value ) )
        {
            $value = (array) $value;
        }

        if ( is_array( $value ) )
        {
            $class = array_shift( $value );

            if ( class_exists( $class ) &&
                 method_exists( $class, '__invoke' ) )
            {
                $class = new ReflectionClass( $class );
                return $class->newInstanceArgs( $value );
            }
        }

        throw new Exception\InvalidArgumentException( sprintf(
            '%s: $value (%s) cannot be converted to a callable',
            __METHOD__,
            is_object( $value ) ? get_class( $value ) : gettype( $value )
        ) );
    }

    public static function toDateTime( $value )
    {
        if ( $value instanceof DateTime )
        {
            return $value;
        }

        if ( $value instanceof \DateTime )
        {
            return DateTime::createFromFormat(
                DateTime::ISO8601,
                $value->format( DateTime::ISO8601 )
            );
        }

        if ( ! is_scalar( $value ) )
        {
            $value = (string) $value;
        }

        if ( is_numeric( $value ) )
        {
            return new DateTime( '@' . $value );
        }

        return new DateTime( $value );
    }

}
