<?php

namespace Zork\Mvc\Controller\Plugin;

use Zork\Stdlib\Message;
use Zork\Session\ContainerAwareTrait;
use Zend\Stdlib\SplPriorityQueue;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Messenger
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Messenger extends AbstractPlugin
{

    use ContainerAwareTrait;

    /**
     * @var array
     */
    protected $priorities = array(
        Message::LEVEL_ERROR    => 90,
        Message::LEVEL_WARN     => 60,
        Message::LEVEL_INFO     => 30,
        ''                      => 1,
    );

    /**
     * Get session container for messages
     *
     * @return \Zend\Session\Container
     */
    public function getContainer()
    {
        return $this->getSessionContainer( Message::CONTAINER );
    }

    /**
     * Get messages
     *
     * @return \Zend\Stdlib\SplPriorityQueue
     */
    public function getMessages()
    {
        $container = $this->getContainer();

        if ( ! isset( $container->messages ) )
        {
            $container->messages = new SplPriorityQueue();
        }

        return $container->messages;
    }

    /**
     * Invoke as a functor
     *
     * @param string $message
     * @param string|false $textDomain
     * @param string $level
     * @param int $priority
     * @return \Zork\Mvc\Controller\Plugin\Messenger
     */
    public function __invoke( $message      = null,
                              $textDomain   = Message::DEFAULT_TEXT_DOMAIN,
                              $level        = Message::DEFAULT_LEVEL,
                              $priority     = null )
    {
        if ( $message )
        {
            $this->add( $message, $textDomain, $level, $priority );
        }

        return $this;
    }

    /**
     * Add a message
     *
     * @param string|\Zork\Message $message
     * @param string|false $textDomain
     * @param string $level
     * @return \Zork\Mvc\Controller\Plugin\Messenger
     */
    public function add( $message,
                         $textDomain    = Message::DEFAULT_TEXT_DOMAIN,
                         $level         = Message::DEFAULT_LEVEL,
                         $priority      = null  )
    {
        if ( ! $message instanceof Message )
        {
            $message = new Message( $message, $textDomain, $level );
        }

        if ( null === $priority )
        {
            $priority = isset( $this->priorities[$level] )
                ? $this->priorities[$level]
                : $this->priorities[''];
        }

        $this->getMessages()
             ->insert( $message, $priority );

        return $this;
    }

}
