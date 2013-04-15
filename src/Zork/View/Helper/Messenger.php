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

        if ( isset( $container->messages ) )
        {
            $messages = $container->messages;
            unset( $container->messages );
        }
        else
        {
            $messages = new SplPriorityQueue;
        }

        return $messages;
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
