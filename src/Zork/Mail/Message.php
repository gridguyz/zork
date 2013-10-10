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
    const DEFAULT_ENCODING = 'UTF-8';

    /**
     * Default user-agent for send()
     *
     * @var string
     */
    const USER_AGENT = 'Zork_Mail/1.1';

    /**
     * Message encoding
     *
     * Used to determine whether or not to encode headers; defaults to UTF-8.
     *
     * @var string
     */
    protected $encoding = self::DEFAULT_ENCODING;

    /**
     * Set the message body
     *
     * @param   null|string|\Zend\Mime\Message|\Traversable|array   $body
     * @param   bool                                                $generateText
     * @param   bool                                                $alternative
     * @throws  \Zend\Mail\Exception\InvalidArgumentException
     * @return  \Zork\Mail\Message
     */
    public function setBody( $body, $generateText = true, $alternative = true )
    {
        static $mimeAliases = array(
            'text'          => Mime\Mime::TYPE_TEXT,
            'html'          => Mime\Mime::TYPE_HTML,
            'alternative'   => Mime\Mime::MULTIPART_ALTERNATIVE,
            'mixed'         => Mime\Mime::MULTIPART_MIXED,
            'related'       => Mime\Mime::MULTIPART_RELATED,
        );

        if ( $body !== null )
        {
            if ( is_scalar( $body ) )
            {
                $body = array(
                    'text/html' => (string) $body,
                );
            }

            if ( ! $body instanceof Mime\Message )
            {
                $message = new Mime\Message;

                if ( $body instanceof Mime\Part )
                {
                    if ( empty( $body->charset ) )
                    {
                        $body->charset = $this->getEncoding();
                    }

                    $message->addPart( $body );
                }
                else
                {
                    foreach ( $body as $type => $content )
                    {
                        if ( isset( $mimeAliases[$type] ) )
                        {
                            $type = $mimeAliases[$type];
                        }

                        if ( $content instanceof Mime\Message )
                        {
                            /* @var $content \Zend\Mime\Message */
                            if ( $content->isMultiPart() )
                            {
                                $mime = $content->getMime();
                                $part = new Mime\Part(
                                    $content->generateMessage( Headers::EOL )
                                );

                                if ( ! preg_match( '#^multipart/#', $type ) )
                                {
                                    $type = Mime\Mime::MULTIPART_MIXED;
                                }

                                $part->type     = $type;
                                $part->boundary = $mime->boundary();
                            }
                            else
                            {
                                $parts  = $content->getParts();
                                $part   = reset( $parts );
                            }
                        }
                        else if ( $content instanceof Mime\Part )
                        {
                            /* @var $content \Zend\Mime\Part */
                            $part = $content;
                        }
                        else
                        {
                            $part = new Mime\Part( $content );
                            $part->type     = $type;
                            $part->charset  = $this->getEncoding();
                        }

                        if ( empty( $part->type ) )
                        {
                            $part->type = $type;
                        }

                        if ( empty( $part->charset ) )
                        {
                            $part->charset = $this->getEncoding();
                        }

                        $message->addPart( $part );
                    }
                }

                $body = $message;
            }

            /* @var $body \Zend\Mime\Message */
            $partHtml   = null;
            $partText   = null;
            $parts      = $body->getParts();

            foreach ( $parts as $part )
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
                array_unshift( $parts, $partText );
                $body->setParts( $parts );
            }
        }

        parent::setBody( $body );

        if ( $alternative &&
             $body instanceof Mime\Message &&
             $body->isMultiPart() )
        {
            $this->getHeaderByName( 'content-type', 'Zend\Mail\Header\ContentType' )
                 ->setType( Mime\Mime::MULTIPART_ALTERNATIVE )
                 ->addParameter( 'boundary', $body->getMime()->boundary() );
        }

        return $this;
    }

    /**
     * Compose headers
     *
     * @param   \Zend\Mail\Headers  $headers
     * @return  \Zork\Mail\Message
     */
    public function setHeaders( Headers $headers )
    {
        $headers->addHeaderLine( 'User-Agent',  static::USER_AGENT )
                ->addHeaderLine( 'X-Mailer',    static::USER_AGENT );

        return parent::setHeaders( $headers );
    }

}
