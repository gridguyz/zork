<?php

namespace Zork\Mail\ServiceTest;

use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;

class TransportTestOptions
{

    /**
     * @var callable
     */
    public $callback;

    /**
     * @param array $options
     */
    public function __construct( array $options )
    {
        if ( isset( $options['callback'] ) &&
             is_callable( $options['callback'] ) )
        {
            $this->callback = $options['callback'];
        }
    }

}

class TransportTest implements TransportInterface
{

    /**
     * @var callable
     */
    public $callback;

    /**
     * @param \Zork\Mail\ServiceTest\TransportTestOptions $options
     */
    public function __construct( TransportTestOptions $options = null )
    {
        $this->callback = $options->callback ?: array( $this, 'noop' );
    }

    /**
     * Noop
     */
    public function noop()
    {
        // dummy
    }

    /**
     * @param \Zend\Mail\Message $message
     */
    public function send( Message $message )
    {
        $callback = $this->callback;
        return $callback( $message, $this );
    }

}
