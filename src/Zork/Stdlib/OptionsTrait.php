<?php

namespace Zork\Stdlib;

/**
 * OptionsTrait
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait OptionsTrait
{

    /**
     * Options array
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Set multiple options
     *
     * @param array $options
     * @return self
     */
    public function setOptions( $options )
    {
        foreach ( $options as $key => $value )
        {
            $this->setOption( $key, $value );
        }

        return $this;
    }

    /**
     * Get the options object
     *
     * @return object
     */
    public function getOptions()
    {
        return (object) $this->_options;
    }

    /**
     * Set an option
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setOption( $key, $value )
    {
        $method = 'set' . ucfirst(
            isset( $key[0] ) && $key[0] == '_' ? substr( $key, 1 ) : $key
        );

        if ( is_callable( array( $this, $method ) ) )
        {
            $this->$method( $value );
        }
        else if ( $key[0] != '_' && property_exists( $this, $key ) )
        {
            $this->$key = $value;
        }

        $this->_options[$key] = $value;
        return $this;
    }

    /**
     * Get an option
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getOption( $key, $default = null )
    {
        return isset( $this->_options[$key] )
                    ? $this->_options[$key] : $default;
    }

}
