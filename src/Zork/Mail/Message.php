<?php

namespace Zork\Mail;

use Zend\Mime;
use Zend\Mail\Headers;
use Zork\Stdlib\String;
use Zend\Mail\Message as ZendMessage;

/**
 * Message
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Message extends ZendMessage
{

    /**
     * Default charset for the constructor
     *
     * @var string
     */
    const DEFAULT_ENCODING  = 'UTF-8';

    /**
     * Default user-agent for send()
     *
     * @var string
     */
    const USER_AGENT        = 'Zork_Mail/1.0';

    /**
     * Message encoding
     *
     * Used to determine whether or not to encode headers; defaults to UTF-8.
     *
     * @var string
     */
    protected $encoding     = self::DEFAULT_ENCODING;

    /**
     * Set the message body
     *
     * @param  null|string|\Zend\Mime\Message|\Traversable|object|array $body
     * @param  bool $generateText default: true
     * @throws \Zend\Mail\Exception\InvalidArgumentException
     * @return \Zork\Mail\Message
     */
    public function setBody( $body, $generateText = true )
    {
        if ( $body !== null )
        {
            if ( is_string( $body ) )
            {
                $body = array(
                    'text/html' => $body,
                );
            }

            if ( is_array( $body ) ||
                 $body instanceof \stdClass ||
                 $body instanceof \Traversable )
            {
                $message = new Mime\Message;

                foreach ( $body as $type => $content )
                {
                    $part = new Mime\Part( $content );
                    $part->type     = $type;
                    $part->charset  = $this->getEncoding();
                    $message->addPart( $part );
                }

                $body = $message;
            }

            if ( $body instanceof Mime\Message )
            {
                $partHtml = null;
                $partText = null;

                foreach ( $body->getParts() as $part )
                {
                    /* @var $part \Zend\Mime\Part */
                    switch ( $part->type )
                    {
                        case Mime\Mime::TYPE_HTML:
                            $partHtml = $part;
                            break;

                        case Mime\Mime::TYPE_TEXT:
                            $partText = $part;
                            break;
                    }
                }

                if ( $generateText && empty( $partText ) && ! empty( $partHtml ) )
                {
                    $partText = new Mime\Part( String::stripHtml(
                        $partHtml->getContent( Headers::EOL ),
                        $this->getEncoding()
                    ) );

                    $partText->type     = Mime\Mime::TYPE_TEXT;
                    $partText->charset  = $this->getEncoding();
                    $body->addPart( $partText );
                }
            }
        }

        return parent::setBody( $body );
    }

    /**
     * Compose headers
     *
     * @param  \Zend\Mail\Headers $headers
     * @return \Zork\Mail\Message
     */
    public function setHeaders( Headers $headers )
    {
        $headers->addHeaderLine( 'User-Agent', static::USER_AGENT )
                ->addHeaderLine( 'X-Mailer', static::USER_AGENT );

        return parent::setHeaders( $headers );
    }

}
