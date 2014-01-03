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
            $contentType = strtolower( $part->type );

            if ( empty( $contentType ) )
            {
                foreach ( $part->getHeadersArray() as $header )
                {
                    list( $field, $value ) = $header;

                    if ( strtolower( $field ) === 'content-type' )
                    {
                        $contentType = strtolower(
                            preg_replace( '/^\s*([^;]+).*$/', '$1', $value )
                        );

                        break;
                    }
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
     * Test body mime-type aliases
     */
    public function testBodyMimeTypeAliases()
    {
        $set = array(
            'text' => 'foo',
            'html' => 'bar',
        );

        $get = array(
            'text/plain' => 'foo',
            'text/html'  => 'bar',
        );

        $this->message->setBody( $set );
        $body = $this->message->getBody();

        $this->assertInstanceOf( 'Zend\Mime\Message', $body );
        $this->assertMimeMessages( $get, $body );
    }

    /**
     * Test body is Mime\Part
     */
    public function testBodyIsMimePart()
    {
        $encoding = 'utf-8';
        $part = new Mime\Part( '<b>foo-bar</b>' );
        $part->type = 'text/html';

        $this->message
             ->setEncoding( $encoding )
             ->setBody( $part );

        $this->assertEquals( $encoding, $part->charset );

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
     * Test body in advanced hierarchy
     */
    public function testBodyAdvanced()
    {
        $message1       = new Mime\Message;
        $htmlPart       = new Mime\Part( 'simple <b>html</b>' );
        $htmlPart->type = 'text/html';
        $textPart       = new Mime\Part( 'simple text' );
        $textPart->type = 'text/plain';
        $emptPart       = new Mime\Part( 'simple text alternative' );
        $emptPart->type = '';

        $message1->addPart( $htmlPart );
        $message2 = clone $message1;
        $message2->addPart( $textPart );

        $this->message->setBody( array(
            $message1,
            $message2,
            'text/plain' => $emptPart,
        ) );

        $body = $this->message->getBody();

        $this->assertInstanceOf( 'Zend\Mime\Message', $body );
        $this->assertMimeMessages(
            array(
                'multipart/mixed'   => $message2->generateMessage( \Zend\Mail\Headers::EOL ),
                'text/html'         => 'simple <b>html</b>',
                'text/plain'        => 'simple text alternative',
            ),
            $body
        );
    }

}
