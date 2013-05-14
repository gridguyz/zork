<?php

namespace Zork\Http\PhpEnvironment\Response;

use Zend\Http\Exception;
use Zend\Stdlib\ErrorHandler;
use Zend\Http\PhpEnvironment\Response;

/**
 * Represents an HTTP response message as PHP stream resource
 *
 * @package    Zork
 * @subpackage Http
 */
class Readfile extends Response
{

    /**
     * File to read
     *
     * @var     string
     */
    protected $file;

    /**
     * Unlink file after read it?
     *
     * @var     bool
     */
    protected $unlink = false;

    /**
     * Constructor
     *
     * @param   string  $file
     * @param   bool    $unlink
     */
    public function __construct( $file, $unlink = false )
    {
        $this->file = $file;
        $this->setUnlink( $unlink );
    }

    /**
     * Populate object from file
     *
     * @param   string      $file
     * @param   string      $mime
     * @param   bool|string $attachment
     * @param   bool        $unlink
     * @return  \Zork\Http\PhpEnvironment\Response\Readfile
     * @throws  \Zend\Http\Exception\InvalidArgumentException
     */
    public static function fromFile( $file, $mime = null, $attachment = false, $unlink = false )
    {
        if ( ! is_file( $file ) || filesize( $file ) < 1 )
        {
            throw new Exception\InvalidArgumentException(
                '$file must be a valid file with size at least 1 byte'
            );
        }

        $readfile = new static( $file, $unlink );

        if ( null === $mime &&
             class_exists( 'finfo', false ) )
        {
            $finfo = @ finfo_open(
                defined( 'FILEINFO_MIME_TYPE' )
                    ? FILEINFO_MIME_TYPE
                    : FILEINFO_MIME
            );

            if ( ! empty( $finfo ) )
            {
                $mime = finfo_file( $finfo, $file );
            }
        }

        if ( null !== $mime )
        {
            $readfile->getHeaders()
                     ->addHeaderLine( 'Content-Type', $mime );
        }

        if ( $attachment )
        {
            if ( ! is_string( $attachment ) || $attachment === '1' )
            {
                $attachment = $file;
            }

            $filename = basename( $attachment );

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
                $readfile->getHeaders()
                         ->addHeaderLine( 'Content-Disposition',
                                          'attachment; filename="'
                                          . str_replace( '"', '\\"', $filename )
                                          . '"' );
            }
        }

        return $readfile;
    }

    /**
     * Set unlink file
     *
     * @return  bool
     */
    public function getUnlink()
    {
        return $this->unlink;
    }

    /**
     * Set unlink file
     *
     * @param   bool    $value
     * @return  Readfile
     */
    public function setUnlink( $value )
    {
        $this->unlink = (bool) $value;
        return $this;
    }

    /**
     * Set message content
     *
     * @param   mixed   $value
     * @return  Readfile
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
     * @return  mixed
     */
    public function getContent()
    {
        return file_get_contents( $this->file );
    }

    /**
     * Send content
     *
     * @return  Response
     */
    public function sendContent()
    {
        if ( $this->contentSent() )
        {
            return $this;
        }

        ErrorHandler::start();
        readfile( $this->file );

        if ( $this->unlink )
        {
            @ unlink( $this->file );
        }

        ErrorHandler::stop( true );

        $this->contentSent = true;
        return $this;
    }

}
