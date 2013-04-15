<?php

namespace Zork\Http\PhpEnvironment\Response;

use Zend\Http\Exception;
use Zork\Data\FileInterface;
use Zend\Http\PhpEnvironment\Response;

/**
 * Represents an HTTP response message as PHP stream resource
 *
 * @package    Zork
 * @subpackage Http
 */
class FileData extends Response
{

    /**
     * File to read
     *
     * @var \Zork\Data\FileInterface
     */
    protected $file;

    /**
     * Constructor
     *
     * @param \Zork\Data\FileInterface $file
     */
    public function __construct( FileInterface $file )
    {
        $this->file = $file;
    }

    /**
     * Populate object from file-data
     *
     * @param  \Zork\Data\FileInterface $file
     * @param  bool|string              $attachment
     * @return \Zork\Http\PhpEnvironment\Response\Readfile
     * @throws \Zend\Http\Exception\InvalidArgumentException
     */
    public static function fromData( FileInterface $file, $attachment = false )
    {
        $filedata   = new static( $file );
        $mime       = $file->getMimeType();

        if ( null !== $mime )
        {
            $filedata->getHeaders()
                     ->addHeaderLine( 'Content-Type', $mime );
        }

        if ( $attachment && is_string( $attachment ) )
        {
            if ( ! is_string( $attachment ) )
            {
                $attachment = (string) $attachment;
            }

            $filename = basename( $attachment );

            if ( function_exists( 'iconv' ) )
            {
                $filename = @ iconv( 'UTF-8', 'ASCII//TRANSLIT', $filename );
            }
            else
            {
                $filename = @ mb_convert_encoding( $filename, 'ASCII', 'auto' );
            }

            if ( ! empty( $filename ) )
            {
                $filedata->getHeaders()
                         ->addHeaderLine( 'Content-Disposition',
                                          'attachment; filename="'
                                          . str_replace( '"', '\\"', $filename )
                                          . '"' );
            }
        }

        return $filedata;
    }

    /**
     * Set message content
     *
     * @param  mixed $value
     * @return Readfile
     */
    public function setContent( $value )
    {
        throw new Exception\RuntimeException(
            'Content cannot be set manually on ' . __CLASS__
        );
    }

    /**
     * Get message content
     *
     * @return string
     */
    public function getContent()
    {
        $content = '';

        foreach ( $this->file as $piece )
        {
            $content .= $piece;
        }

        return $content;
    }

    /**
     * Send content
     *
     * @return Response
     */
    public function sendContent()
    {
        if ( $this->contentSent() )
        {
            return $this;
        }

        foreach ( $this->file as $piece )
        {
            echo $piece;
        }

        $this->contentSent = true;
        return $this;
    }

}
