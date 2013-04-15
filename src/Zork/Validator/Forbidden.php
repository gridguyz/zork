<?php

namespace Zork\Validator;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * Forbidden
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Forbidden extends AbstractValidator
{

    /**
     * @const string
     */
    const FORBIDDEN = 'forbidden';

    /**
     * standard in_array strict checking value and type
     *
     * @const int
     */
    const COMPARE_STRICT = 1;

    /**
     * Non strict check but prevents "asdf" == 0 returning TRUE causing false/positive.
     * This is the most secure option for non-strict checks and replaces strict = false
     * This will only be effective when the input is a string
     *
     * @const int
     */
    const COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY = 0;

    /**
     * Standard non-strict check where "asdf" == 0 returns TRUE
     * This will be wanted when comparing "0" against int 0
     *
     * @const int
     */
    const COMPARE_NOT_STRICT = -1;


    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::FORBIDDEN => 'validate.forbidden.forbidden',
    );

    /**
     * Haystack of forbidden values
     *
     * @var array
     */
    protected $haystack;

    /**
     * Type of strict check to be used. Due to "foo" == 0 === TRUE with in_array when strict = false,
     * an option has been added to prevent this. When $strict = 0/false, the most
     * secure non-strict check is implemented. if $strict = -1, the default in_array non-strict
     * behaviour is used
     *
     * @var int
     */
    protected $strict = self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY;

    /**
     * Whether a recursive search should be done
     *
     * @var boolean
     */
    protected $recursive = false;

    /**
     * Returns the haystack option
     *
     * @return mixed
     * @throws Exception\RuntimeException if haystack option is not set
     */
    public function getHaystack()
    {
        if ( $this->haystack == null )
        {
            throw new Exception\RuntimeException( 'haystack option is mandatory' );
        }

        return $this->haystack;
    }

    /**
     * Sets the haystack option
     *
     * @param  mixed $haystack
     * @return \Zork\Validator\Forbidden
     */
    public function setHaystack( array $haystack )
    {
        $this->haystack = $haystack;
        return $this;
    }

    /**
     * Returns the strict option
     *
     * @return boolean|int
     */
    public function getStrict()
    {
        // To keep BC with new strict modes
        if ( $this->strict == self::COMPARE_STRICT ||
             $this->strict == self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY )
        {
            return (bool) $this->strict;
        }

        return $this->strict;
    }

    /**
     * Sets the strict option mode
     * InArray::CHECK_STRICT | InArray::CHECK_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY | InArray::CHECK_NOT_STRICT
     *
     * @param  int $strict
     * @return InArray Provides a fluent interface
     * @throws Exception\InvalidArgumentException
     */
    public function setStrict($strict)
    {
        $checkTypes = array(
            self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY,    // 0
            self::COMPARE_STRICT,                                             // 1
            self::COMPARE_NOT_STRICT                                          // -1
        );

        // validate strict value
        if ( ! in_array( $strict, $checkTypes ) )
        {
            throw new Exception\InvalidArgumentException(
                'Strict option must be one of the COMPARE_ constants'
            );
        }

        $this->strict = $strict;
        return $this;
    }

    /**
     * Returns the recursive option
     *
     * @return boolean
     */
    public function getRecursive()
    {
        return $this->recursive;
    }

    /**
     * Sets the recursive option
     *
     * @param  boolean $recursive
     * @return InArray Provides a fluent interface
     */
    public function setRecursive($recursive)
    {
        $this->recursive = (boolean) $recursive;
        return $this;
    }

    /**
     * Returns true if and only if $value is contained in the haystack option. If the strict
     * option is true, then the type of $value is also checked.
     *
     * @param mixed $value
     * See {@link http://php.net/manual/function.in-array.php#104501}
     * @return boolean
     */
    public function isValid( $value )
    {
        // we create a copy of the haystack in case we need to modify it
        $haystack = $this->getHaystack();

        // if the input is a string or float, and vulnerability protection is on
        // we type cast the input to a string
        if ( ( is_int( $value ) || is_float( $value ) ) &&
             self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY == $this->strict )
        {
            $value = (string) $value;
        }

        $this->setValue( $value );

        if ( $this->getRecursive() )
        {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveArrayIterator( $haystack )
            );

            foreach ( $iterator as $element )
            {
                if ( self::COMPARE_STRICT == $this->strict )
                {
                    if ( $element === $value )
                    {
                        $this->error( self::FORBIDDEN );
                        return false;
                    }
                }
                else
                {
                    // add protection to prevent string to int vuln's
                    $el = $element;
                    if ( is_string( $value ) && ( is_int( $el ) || is_float( $el ) &&
                         self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY == $this->strict ) )
                    {
                        $el = (string) $el;
                    }

                    if ( $el == $value )
                    {
                        $this->error( self::FORBIDDEN );
                        return false;
                    }
                }
            }
        }
        else
        {
            /**
             * If the check is not strict, then, to prevent "asdf" being converted to 0
             * and returning a false positive if 0 is in haystack, we type cast
             * the haystack to strings. To prevent "56asdf" == 56 === TRUE we also
             * type cast values like 56 to strings as well.
             *
             * This occurs only if the input is a string and a haystack member is an int
             */
            if ( is_string( $value ) &&
                 self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY == $this->strict )
            {
                foreach ( $haystack as &$h )
                {
                    if ( is_int( $h ) || is_float( $h ) )
                    {
                        $h = (string) $h;
                    }
                }
            }

            if ( in_array( $value, $haystack, $this->strict == self::COMPARE_STRICT ? true : false ) )
            {
                $this->error( self::FORBIDDEN );
                return false;
            }
        }

        return true;
    }

}
