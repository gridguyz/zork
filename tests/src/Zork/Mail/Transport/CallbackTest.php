<?php

namespace Zork\Mail\Transport;

use Zork\Mail\Message;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * CallbackTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Mail\Transport\Callback
 * @covers Zork\Mail\Transport\CallbackOptions
 */
class CallbackTest extends TestCase
{

    /**
     * Test options
     */
    public function testOptions()
    {
        $options = new CallbackOptions();
        $this->assertTrue( is_callable( $options->getCallback() ) );
        $this->setExpectedException( 'InvalidArgumentException' );
        $options->setCallback( 0 );
    }

    /**
     * Test send
     */
    public function testSend()
    {
        $count     = 0;
        $self      = $this;
        $transport = new Callback( function ( $message ) use ( &$count, $self ) {
            $self->assertInstanceOf( 'Zend\Mail\Message', $message );
            $count++;
        } );

        $transport->send( new Message );
        $this->assertSame( 1, $count );
    }

}
