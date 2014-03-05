<?php

namespace Zork\Http\PhpEnvironment\Response;

use ZipArchive;
use Zend\Http\Exception;
use Zend\Stdlib\ErrorHandler;
use Zend\Http\PhpEnvironment\Response;

/**
 * Represents an HTTP response message as a ZipArchive
 *
 * @package    Zork
 * @subpackage Http
 */
class Zip extends Response
{

    /**
     * Zip archive
     *
     * @var ZipArchive
     */
    protected $archive;

    /**
     * Closing state
     *
     * @var boolean
     */
    protected $closed = false;

    /**
     * Constructor
     *
     * @param   ZipArchive  $archive
     */
    public function __construct( ZipArchive $archive, $closed = false )
    {
        $this->archive = $archive;
        $this->closed = (bool) $closed;
    }

    /**
     * Populate object from a ZipArchive
     *
     * @param   ZipArchive  $archive
     * @param   bool|string $attachment
     * @param   bool        $closed
     * @return  Zip
     * @throws  \Zend\Http\Exception\InvalidArgumentException
     */
    public static function fromArchive( ZipArchive $archive,
                                        $attachment = false,
                                        $closed     = false )
    {
        $zip = new static( $archive, $closed );

        $zip->getHeaders()
            ->addHeaderLine( 'Content-Type', 'application/zip' );

        if ( $attachment )
        {
            if ( true === $attachment )
            {
                if ( $archive->filename )
                {
                    $attachment = $archive->filename;
                }
                else
                {
                    $attachment = 'attachment.zip';
                }
            }

            $attachment = (string) $attachment;
            $filename   = basename( $attachment );

            // @codeCoverageIgnoreStart

            if ( function_exists( 'iconv' ) )
            {
                $filename = @ iconv( 'UTF-8', 'ASCII//TRANSLIT', $filename );
            }
            else
            {
                $filename = @ mb_convert_encoding( $filename, 'ASCII', 'auto' );
            }

            // @codeCoverageIgnoreEnd

            if ( ! empty( $filename ) )
            {
                $zip->getHeaders()
                    ->addHeaderLine( 'Content-Disposition',
                                     'attachment; filename="'
                                     . str_replace( '"', '\\"', $filename )
                                     . '"' );
            }
        }

        return $zip;
    }

    /**
     * Set message content
     *
     * @param   mixed $value
     * @return  Zip
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
        $archive = $this->archive;

        if ( ! $archive )
        {
            return null;
        }

        if ( ! $this->closed )
        {
            $this->closed = $archive->close();
        }

        return file_get_contents( $archive->filename );
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

        $archive = $this->archive;

        if ( ! $this->closed )
        {
            $this->closed = $archive->close();
        }

        $file = $archive->filename;

        ErrorHandler::start();
        readfile( $file );
        $this->archive = $archive = null;
        @ unlink( $file );
        ErrorHandler::stop( true );

        $this->contentSent = true;
        return $this;
    }

}
