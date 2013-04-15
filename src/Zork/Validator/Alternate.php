<?php

namespace Zork\Validator;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\AbstractValidator;

/**
 * Alternate
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Alternate extends AbstractValidator
{

    /**
     * @const string
     */
    const NONE_PROVIDED = 'noneProvided';

    /**
     * @const string
     */
    const MISSING_TOKEN = 'missingToken';

    /**
     * Error messages
     * @var array
     */
    protected $messageTemplates = array(
        self::NONE_PROVIDED => 'validate.alternate.noneProvided',
        self::MISSING_TOKEN => 'validate.alternate.missingToken',
    );

    /**
     * @var array
     */
    protected $messageVariables = array(
        'token' => 'tokenString'
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
     * @return \Zork\Validator\Alternate
     */
    public function setToken( $token )
    {
        $this->tokenString = ( is_array( $token ) ? implode( $token ) : (string) $token );
        $this->token       = $token;
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

        if ( empty( $value ) && empty( $token ) )
        {
            $this->error( self::NONE_PROVIDED );
            return false;
        }

        return true;
    }
}
