<?php

namespace Zork\View\Helper;

use Zork\Stdlib\Message;
use Zork\Session\ContainerAwareTrait;
use Zend\Stdlib\SplPriorityQueue;
use Zend\View\Helper\AbstractHelper;

/**
 * Messenger
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Messenger extends AbstractHelper
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
     * @var \Zend\Stdlib\SplPriorityQueue
     */
    protected $messages;

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
        if ( $this->messages )
        {
            return $this->messages;
        }

        $container = $this->getContainer();

        if ( isset( $container->messages ) )
        {
            $messages = $container->messages;
            unset( $container->messages );
        }
        else
        {
            $messages = new SplPriorityQueue;
        }

        return $this->messages = $messages;
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

    /**
     * Invoke as a functor
     *
     * @param string $message
     * @param string|false $textDomain
     * @param string $level
     * @return \Zend\Stdlib\SplPriorityQueue
     */
    public function __invoke()
    {
        return $this->getMessages();
    }

}
