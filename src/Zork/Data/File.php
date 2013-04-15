<?php

namespace Zork\Data;

use Iterator;

/**
 * File
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class File implements Iterator,
                      FileInterface
{

    /**
     * @const int
     */
    const MAX_LINE_LENGTH = 1024;

    /**
     * @var string
     */
    protected $pathname;

    /**
     * @var resource
     */
    protected $handle;

    /**
     * @var int
     */
    protected $line;

    /**
     * @var string
     */
    protected $current;

    /**
     * Constructor
     *
     * @param   string  $pathname
     * @throws  Exception\InvalidArgumentException
     */
    public function __construct( $pathname )
    {
        if ( ! is_file( $pathname ) )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                '%s: "%s" is not a file',
                __METHOD__,
                $pathname
            ) );
        }

        $this->pathname = $pathname;
    }

    /**
     * Get mime-type of the file
     *
     * @staticvar   finfo   $finfo
     * @return      string
     */
    public function getMimeType()
    {
        static $finfo;
        $mime = null;

        if ( function_exists( 'mime_content_type' ) &&
             ini_get( 'mime_magic.magicfile' ) )
        {
            $mime = mime_content_type( $this->pathname );
        }

        if ( null === $mime &&
             class_exists( 'finfo', false ) )
        {
            if ( null === $finfo )
            {
                $finfo = @ finfo_open(
                    defined( 'FILEINFO_MIME_TYPE' )
                        ? FILEINFO_MIME_TYPE
                        : FILEINFO_MIME
                );
            }

            if ( ! empty( $finfo ) )
            {
                $mime = finfo_file( $finfo, $this->pathname );
            }
        }

        return $mime;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        if ( $this->handle )
        {
            rewind( $this->handle );
        }
        else
        {
            $this->handle = fopen( $this->pathname, 'r' );
        }

        $this->line     = 0;
        $this->current  = fgets( $this->handle );
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    public function valid()
    {
        $lec = microtime( true );

        return $this->handle && ! feof( $this->handle ) && (
            ( microtime( true ) - $lec ) < ini_get( 'default_socket_timeout' )
        );
    }

    /**
     * Return the key of the current element
     *
     * @return scalar scalar on success, or <b>NULL</b> on failure.
     */
    public function key()
    {
        return $this->line;
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        if ( $this->handle )
        {
            $line = fgets( $this->handle );

            if ( $line === false )
            {
                $this->line     = null;
                $this->current  = null;
            }
            else
            {
                $this->line++;
                $this->current  = $line;
            }
        }
    }

    /**
     * Destructor
     * Cleans up the file-handle
     */
    public function __destruct()
    {
        if ( $this->handle )
        {
            fclose( $this->handle );
        }
    }

}
