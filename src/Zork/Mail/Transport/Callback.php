<?php

namespace Zork\Mail\Transport;

use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;

/**
 * Callback
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Callback implements TransportInterface
{

    /**
     * @var CallbackOptions
     */
    protected $options;

    /**
     * Constructor
     *
     * @param   callable|array|CallbackOptions $options
     */
    public function __construct( $options = null )
    {
        if ( null !== $options && ! $options instanceof CallbackOptions )
        {
            $options = new CallbackOptions( $options );
        }

        $this->setOptions( $options );
    }

    /**
     * Set options
     *
     * @param   CallbackOptions $options
     * @return  Callback
     */
    public function setOptions( CallbackOptions $options )
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Send a mail message
     *
     * @param   Message $message
     * @return  mixed
     */
    public function send( Message $message )
    {
        $callback = $this->options->getCallback();
        return $callback( $message, $this );
    }

}
