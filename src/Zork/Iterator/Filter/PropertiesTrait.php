<?php

namespace Zork\Iterator\Filter;

/**
 * Properties trait
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait PropertiesTrait
{

    /**
     * Compare aliases
     *
     * @var array
     */
    protected $cmpAliases = array(
        '='     => PropertiesConsts::CMP_EQUAL,
        '~'     => PropertiesConsts::CMP_REGEXP,
        '<>'    => PropertiesConsts::CMP_NOT_EQUAL,
    );

    /**
     * Checked properties
     *
     * @var array
     */
    protected $properties = array();

    /**
     * Clear properties
     *
     * @return self
     */
    public function clearProperties()
    {
        $this->properties = array();
        return $this;
    }

    /**
     * Add properties-contraint
     *
     * @param   array|object    $properties
     * @return  self
     */
    public function addProperties( $properties )
    {
        $matches = array();
        $chars   = preg_quote( PropertiesConsts::CMP_VALID_CHARS, '#' );
        $regexp  = '#^([A-Za-z_][A-Za-z0-9_]*)\s*([' . $chars . ']*)$#';

        foreach ( $properties as $desc => $value )
        {
            if ( preg_match( $regexp, $desc, $matches ) )
            {
                list( , $property, $cmp ) = $matches;

                if ( empty( $cmp ) )
                {
                    $cmp = PropertiesConsts::CMP_DEFAULT;
                }
                else if ( isset( $this->cmpAliases[$cmp] ) )
                {
                    $cmp = $this->cmpAliases[$cmp];
                }

                if ( empty( $this->properties[$property] ) )
                {
                    $this->properties[$property] = array();
                }

                $this->properties[$property][$cmp] = $value;
            }
        }

        return $this;
    }

    /**
     * Add a property-contraint
     *
     * @param   string  $property
     * @param   mixed   $value
     * @param   string  $cmp
     * @return  self
     */
    public function addProperty( $property,
                                 $value,
                                 $cmp = PropertiesConsts::CMP_DEFAULT )
    {
        if ( empty( $this->properties[$property] ) )
        {
            $this->properties[$property] = array();
        }

        $this->properties[$property][$cmp] = $value;
        return $this;
    }

    /**
     * Set properties
     *
     * @param   array|object    $properties
     * @return  self
     */
    public function setProperties( $properties )
    {
        return $this->clearProperties()
                    ->addProperties( $properties );
    }

    /**
     * Check whether the current element of the iterator is acceptable
     *
     * @return  bool    true if the current element is acceptable,
     *                  otherwise false.
     */
    public function accept()
    {
        $current = $this->current();

        if ( is_array( $current ) )
        {
            $current = (object) $current;
        }

        foreach ( $this->properties as $name => $cmps )
        {
            $property = null;
            $method   = array( $current, 'get' . ucfirst( $name ) );

            switch ( true )
            {
                case isset( $current->$name ):
                    $property = $current->$name;
                    break;

                case is_callable( $method ):
                    $property = call_user_func( $method );
                    break;
            }

            foreach ( $cmps as $cmp => $value )
            {
                switch ( $cmp )
                {
                    case PropertiesConsts::CMP_EQUAL:
                        $result = $property == $value;
                        break;

                    case PropertiesConsts::CMP_IDENTICAL:
                        $result = $property === $value;
                        break;

                    case PropertiesConsts::CMP_REGEXP:
                        $result = (bool) preg_match( $value, $property );
                        break;

                    case PropertiesConsts::CMP_NOT_EQUAL:
                        $result = $property != $value;
                        break;

                    case PropertiesConsts::CMP_NOT_IDENTICAL:
                        $result = $property !== $value;
                        break;

                    case PropertiesConsts::CMP_NOT_REGEXP:
                        $result = ! preg_match( $value, $property );
                        break;

                    case PropertiesConsts::CMP_GREATER_THAN:
                        $result = $property > $value;
                        break;

                    case PropertiesConsts::CMP_GREATER_EQUAL:
                        $result = $property >= $value;
                        break;

                    case PropertiesConsts::CMP_LESSER_THAN:
                        $result = $property < $value;
                        break;

                    case PropertiesConsts::CMP_LESSER_EQUAL:
                        $result = $property <= $value;
                        break;

                    case PropertiesConsts::CMP_CALLBACK:
                        $result = is_callable( $value ) &&
                                  call_user_func( $value, $property );
                        break;

                    default: // No known cmp
                        $result = false;
                        break;
                }

                if ( ! $result )
                {
                    return false;
                }
            }
        }

        return true;
    }

}
