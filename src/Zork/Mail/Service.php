<?php

namespace Zork\Mail;

use Zend\Mail;

/**
 * Service
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Service
{

    /**
     * @var \Zend\Mail\Transport\TransportInterface
     */
    protected $transport;

    /**
     * @var array
     */
    protected $defaultFrom      = array();

    /**
     * @var array
     */
    protected $defaultReplyTo   = array();

    /**
     * @return \Zend\Mail\Transport\TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param \Zend\Mail\Transport\TransportInterface $transport
     * @return \Zork\Mail\Service
     */
    public function setTransport( Mail\Transport\TransportInterface $transport )
    {
        $this->transport = $transport;
        return $this;
    }

    /**
     * @return array
     */
    public function getDefaultFrom()
    {
        return $this->defaultFrom;
    }

    /**
     * @param string|array $emailOrSpec
     * @param string|null $name
     * @return \Zork\Mail\Service
     */
    public function setDefaultFrom( $emailOrSpec, $name = null )
    {
        if ( is_array( $emailOrSpec ) && null === $name )
        {
            $this->defaultFrom = $emailOrSpec;

            if ( empty( $this->defaultFrom['email'] ) )
            {
                $this->defaultFrom['email'] = null;
            }

            if ( empty( $this->defaultFrom['name'] ) )
            {
                $this->defaultFrom['name'] = null;
            }
        }
        else
        {
            $this->defaultFrom = array(
                'email' => ( (string) $emailOrSpec ) ?: null,
                'name'  => ( (string) $name ) ?: null,
            );
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getDefaultReplyTo()
    {
        return $this->defaultReplyTo;
    }

    /**
     * @param string|array $emailOrSpec
     * @param string|null $name
     * @return \Zork\Mail\Service
     */
    public function setDefaultReplyTo( $emailOrSpec, $name = null )
    {
        if ( is_array( $emailOrSpec ) && null === $name )
        {
            $this->defaultReplyTo = $emailOrSpec;

            if ( empty( $this->defaultReplyTo['email'] ) )
            {
                $this->defaultReplyTo['email'] = null;
            }

            if ( empty( $this->defaultReplyTo['name'] ) )
            {
                $this->defaultReplyTo['name'] = null;
            }
        }
        else
        {
            $this->defaultReplyTo = array(
                'email' => ( (string) $emailOrSpec ) ?: null,
                'name'  => ( (string) $name ) ?: null,
            );
        }

        return $this;
    }

    /**
     * @param array $options
     * @return \Zork\Mail\Service
     */
    public static function factory( array $options )
    {
        $service = new static;

        if ( ! empty( $options['transport']['type'] ) )
        {
            $transportOptions = null;

            if ( ! empty( $options['transport']['options']['type'] ) )
            {
                $class = $options['transport']['options']['type'];
                $transportOptionsOptions = null;

                if ( ! empty( $options['transport']['options']['options'] ) )
                {
                    $transportOptionsOptions =
                        (array) $options['transport']['options']['options'];
                }

                $transportOptions = new $class( $transportOptionsOptions );
            }

            $class = $options['transport']['type'];
            $service->setTransport( new $class( $transportOptions ) );
        }

        if ( ! empty( $options['defaultFrom'] ) )
        {
            $service->setDefaultFrom( (array) $options['defaultFrom'] );
        }

        if ( ! empty( $options['defaultReplyTo'] ) )
        {
            $service->setDefaultReplyTo( (array) $options['defaultReplyTo'] );
        }

        return $service;
    }

    /**
     * @param array|\Traversable|\Zend\Mail\Message $message
     * @return void|mixed the transport's response
     */
    public function send( $message )
    {
        return $this->getTransport()
                    ->send( $this->createMessage( $message ) );
    }

    /**
     * @param array|\Traversable|\Zend\Mail\Message $message
     * @return \Zend\Mail\Message
     */
    public function createMessage( $message )
    {
        if ( ! $message instanceof Mail\Message )
        {
            $mail = new Message();

            foreach ( $message as $option => $value )
            {
                $method = array( $mail, 'set' . ucfirst( $option ) );

                if ( is_callable( $method ) )
                {
                    $method( $value );
                }
                else
                {
                    $mail->getHeaders()
                         ->addHeaderLine( $option, $value );
                }
            }

            $message = $mail;
        }

        $from = $message->getFrom();

        if ( empty( $from ) || $from->count() < 1 )
        {
            $defaultFrom    = $this->getDefaultFrom();
            $defaultReplyTo = $this->getDefaultReplyTo();
            $message->setFrom( $defaultFrom['email'], $defaultFrom['name'] );
            $message->setReplyTo( $defaultReplyTo['email'], $defaultReplyTo['name'] );
        }

        return $message;
    }

}
