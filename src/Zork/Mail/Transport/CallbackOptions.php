<?php

namespace Zork\Mail\Transport;

use Zend\Mail\Exception;
use Zend\Stdlib\AbstractOptions;

/**
 * CallbackOptions
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CallbackOptions extends AbstractOptions
{

    /**
     * @var callable
     */
    protected $callback;

    /**
     * Constructor
     *
     * @param  callable|array|Traversable|null  $options
     */
    public function __construct( $options = null )
    {
        if ( is_callable( $options ) )
        {
            $options = array(
                'callback' => $options,
            );
        }

        parent::__construct( $options );
    }

    /**
     * Set callback used to send the message
     *
     * @param   callable    $callback
     * @throws  Exception\InvalidArgumentException
     * @return  CallbackOptions
     */
    public function setCallback( $callback )
    {
        if ( ! is_callable( $callback ) )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                '%s expects a valid callback; received "%s"',
                __METHOD__,
                is_object( $callback ) ? get_class( $callback ) : gettype( $callback )
            ) );
        }

        $this->callback = $callback;
        return $this;
    }

    /**
     * Get callback used to send the message
     *
     * @return callable
     */
    public function getCallback()
    {
        if ( null === $this->callback )
        {
            $this->setCallback( array( __CLASS__, 'noop' ) );
        }

        return $this->callback;
    }

    /**
     * Noop
     *
     * @codeCoverageIgnore
     * @ignore
     */
    final public static function noop()
    {
        // dummy
    }

}
