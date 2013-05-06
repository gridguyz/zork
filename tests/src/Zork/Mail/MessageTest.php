<?php

namespace Zork\Mail;

use Zend\Mime;
use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * MessageTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Mail\Message
 */
class MessageTest extends TestCase
{

    /**
     * @var Message
     */
    protected $message;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->message = new Message;
    }

    /**
     * Test default user-agent
     */
    public function testUserAgent()
    {
        /* @var $userAgent \Zend\Mail\Header\HeaderInterface */
        $userAgent = $this->message
                          ->getHeaders()
                          ->get( 'User-Agent' );

        $this->assertEquals( Message::USER_AGENT, $userAgent->getFieldValue() );
    }

    /**
     * Test body generate text
     */
    public function testBodyGenerateText()
    {
        /* @var $body \Zend\Mime\Message */
        $this->message->setBody( '<b>foo-bar</b>' );
        $body = $this->message->getBody();

        $this->assertInstanceOf( 'Zend\Mime\Message', $body );
        $this->assertMimeMessages(
            array(
                'text/html'     => '<b>foo-bar</b>',
                'text/plain'    => 'foo-bar',
            ),
            $body
        );
    }

    /**
     * Test body custom mime-types
     */
    public function testBodyCustomMimeTypes()
    {
        $bodies = array(
            'text/plain'                => 'foo',
            'application/octet-stream'  => 'bar',
        );

        $this->message->setBody( new ArrayIterator( $bodies ) );
        $body = $this->message->getBody();

        $this->assertInstanceOf( 'Zend\Mime\Message', $body );
        $this->assertMimeMessages( $bodies, $body );
    }

    /**
     * Assert mime messages
     *
     * @param   array               $expected
     * @param   \Zend\Mime\Message  $actual
     */
    public static function assertMimeMessages( $expected, Mime\Message $actual )
    {
        $found = array();

        foreach ( $actual->getParts() as $part )
        {
            /* @var $part \Zend\Mime\Part */
            static::assertInstanceOf( 'Zend\Mime\Part', $part );
            $contentType = null;

            foreach ( $part->getHeadersArray() as $header )
            {
                list( $field, $value ) = $header;

                if ( strtolower( $field ) === 'content-type' )
                {
                    $contentType = strtolower(
                        preg_replace( '/\s*(;.*)?$/', '', $value )
                    );

                    break;
                }
            }

            foreach ( $expected as $mimeType => $rawContent )
            {
                if ( strtolower( $mimeType ) == $contentType )
                {
                    static::assertEquals(
                        $rawContent,
                        $part->getRawContent(),
                        sprintf(
                            'Content do not match in mime-type "%s"',
                            $mimeType
                        )
                    );

                    $found[$mimeType] = true;
                }
            }
        }

        foreach ( $expected as $mimeType => $rawContent )
        {
            static::assertFalse(
                empty( $found[$mimeType] ),
                sprintf(
                    'Content not found in mime-type "%s"',
                    $mimeType
                )
            );
        }
    }

}
