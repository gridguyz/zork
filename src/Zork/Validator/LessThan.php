<?php

namespace Zork\Validator;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\AbstractValidator;

/**
 * LessThan
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class LessThan extends AbstractValidator
{

    /**
     * @const string
     */
    const NOT_LESS_THAN             = 'notLessThan';

    /**
     * @const string
     */
    const NOT_LESS_THAN_OR_EQUAL    = 'notLessThanOrEqual';

    /**
     * @const string
     */
    const MISSING_TOKEN             = 'missingToken';

    /**
     * Error messages
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_LESS_THAN             => 'validate.lessThan.notLess',
        self::NOT_LESS_THAN_OR_EQUAL    => 'validate.lessThan.notLessOrEqual',
        self::MISSING_TOKEN             => 'validate.lessThan.missingToken',
    );

    /**
     * @var array
     */
    protected $messageVariables = array(
        'token' => 'tokenString',
    );

    /**
     * Original token against which to validate
     * @var string
     */
    protected $tokenString;

    /**
     * @var mixed
     */
    protected $token;

    /**
     * Equality test
     * @var bool
     */
    protected $equal = false;

    /**
     * Sets validator options
     *
     * @param mixed $token
     */
    public function __construct( $token = null )
    {
        if ( $token instanceof Traversable )
        {
            $token = ArrayUtils::iteratorToArray( $token );
        }

        if ( is_array( $token ) && array_key_exists( 'token', $token ) )
        {
            if ( array_key_exists( 'equal', $token ) )
            {
                $this->setEqual( $token['equal'] );
            }

            $this->setToken( $token['token'] );
        }
        elseif ( null !== $token )
        {
            $this->setToken( $token );
        }

        parent::__construct( is_array( $token ) ? $token : null );
    }

    /**
     * Retrieve token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set token against which to compare
     *
     * @param  mixed $token
     * @return \Zork\Validator\LessThan
     */
    public function setToken( $token )
    {
        $this->tokenString = ( is_array( $token ) ? implode( $token ) : (string) $token );
        $this->token       = $token;
        return $this;
    }

    /**
     * Retrieve equal
     *
     * @return string
     */
    public function getEqual()
    {
        return $this->equal;
    }

    /**
     * Set equal
     *
     * @param  bool $equal
     * @return Identical
     */
    public function setEqual( $equal )
    {
        $this->equal = (bool) $equal;
        return $this;
    }

    /**
     * Returns true if and only if a token, or the provided value is not empty.
     *
     * @param  mixed $value
     * @param  array $context
     * @return boolean
     */
    public function isValid( $value, $context = null )
    {
        $this->setValue( $value );

        if ( ( $context !== null ) && isset( $context ) &&
             array_key_exists( $this->getToken(), $context ) )
        {
            $token = $context[$this->getToken()];
        }
        else
        {
            $this->error( self::MISSING_TOKEN );
            return false;
        }

        if ( ! empty( $value ) && ! empty( $token ) )
        {
            if ( $this->getEqual() )
            {
                if ( $value > $token )
                {
                    $this->error( self::NOT_LESS_THAN_OR_EQUAL );
                    return false;
                }
            }
            else
            {
                if ( $value >= $token )
                {
                    $this->error( self::NOT_LESS_THAN );
                    return false;
                }
            }
        }

        return true;
    }

}
