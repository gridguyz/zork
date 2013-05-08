<?php

namespace Zork\View\Helper;

use Zend\Stdlib\SplPriorityQueue;
use Zend\Session\SessionManager;
use Zend\Session\Storage\ArrayStorage;
use Zork\Test\PHPUnit\View\Helper\TestCase;

/**
 * MessengerTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\View\Helper\Messenger
 */
class MessengerTest extends TestCase
{

    /**
     * @var string
     */
    protected static $helperClass = 'Zork\View\Helper\Messenger';

    /**
     * Test messages
     */
    public function testMessages()
    {
        $this->assertInstanceOf(
            'Zend\Session\Container',
            $defaultContainer = $this->helper->getContainer()
        );

        $this->helper->setSessionManager(
            new SessionManager( null, new ArrayStorage )
        );

        $this->assertInstanceOf(
            'Zend\Session\Container',
            $arrayContainer = $this->helper->getContainer()
        );

        $this->assertNotSame( $defaultContainer, $arrayContainer );

        $this->assertInstanceOf(
            'Zend\Stdlib\SplPriorityQueue',
            $messages1 = $this->helper()
        );

        $this->assertCount( 0, $messages1 );

        /* @var $arrayContainer \Zend\Session\Container */
        $arrayContainer['messages'] = new SplPriorityQueue;
        $arrayContainer['messages']->insert( 'message1', 1 );
        $arrayContainer['messages']->insert( 'message2', 1 );
        $arrayContainer['messages']->insert( 'message3', 1 );

        $this->assertInstanceOf(
            'Zend\Stdlib\SplPriorityQueue',
            $messages2 = $this->helper()
        );

        $this->assertSame(
            array(
                'message1',
                'message2',
                'message3',
            ),
            $messages2->toArray()
        );
    }

}
