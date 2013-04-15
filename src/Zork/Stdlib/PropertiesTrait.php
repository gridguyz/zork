<?php

namespace Zork\Stdlib;

/**
 * PropertiesTrait
 *
 * implements ArrayAccess
 *
 * @see ArrayAccess
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait PropertiesTrait
{

    /**
     * Get a magic-property
     *
     * @param string $key
     * @return mixed
     */
    public function offsetGet( $key )
    {
        $method = 'get' . ucfirst(
            isset( $key[0] ) && $key[0] == '_' ? substr( $key, 1 ) : $key
        );

        if ( is_callable( array( $this, $method ) ) )
        {
            return $this->$method();
        }
        else if ( property_exists( $this, $key ) )
        {
            if ( isset( $key[0] ) && $key[0] != '_' )
            {
                return $this->$key;
            }
            else
            {
                throw new \LogicException(
                    'Property "' . $key . '" is not accessible'
                );
            }
        }

        return null;
    }

    /**
     * Set a magic-property
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function offsetSet( $key, $value )
    {
        $method = 'set' . ucfirst(
            isset( $key[0] ) && $key[0] == '_' ? substr( $key, 1 ) : $key
        );

        if ( is_callable( array( $this, $method ) ) )
        {
            $this->$method( $value );
        }
        else
        {
            throw new \LogicException( 'Read-only property: "' . $key . '"' );
        }

        return $this;
    }

    /**
     * Is a magic-property exists
     *
     * @param string $key
     * @return bool
     */
    public function offsetExists( $key )
    {
        $method = 'isset' . ucfirst(
            isset( $key[0] ) && $key[0] == '_' ? substr( $key, 1 ) : $key
        );

        if ( is_callable( array( $this, $method ) ) )
        {
            return $this->$method();
        }
        else
        {
            return null !== $this->offsetGet( $key );
        }
    }

    /**
     * Unset a magic-property
     *
     * @param string $key
     * @return bool
     */
    public function offsetUnset( $key )
    {
        $method = 'unset' . ucfirst(
            isset( $key[0] ) && $key[0] == '_' ? substr( $key, 1 ) : $key
        );

        if ( is_callable( array( $this, $method ) ) )
        {
            $this->$method();
            return $this;
        }
        else
        {
            return $this->offsetSet( $key, null );
        }
    }

    /**
     * Get a magic-property
     *
     * @param string $key
     * @return mixed
     */
    public function __get( $key )
    {
        return $this->offsetGet( $key );
    }

    /**
     * Set a magic-property
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function __set( $key, $value )
    {
        return $this->offsetSet( $key, $value );
    }

    /**
     * Is a magic-property exists
     *
     * @param string $key
     * @return bool
     */
    public function __isset( $key )
    {
        return $this->offsetExists( $key );
    }

    /**
     * Unset a magic-property
     *
     * @param string $key
     * @return bool
     */
    public function __unset( $key )
    {
        return $this->offsetUnset( $key );
    }

}
