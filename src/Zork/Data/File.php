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
     * @var string
     */
    protected $mimeType;

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
     * @param   string|resource  $pathname
     * @throws  Exception\InvalidArgumentException
     */
    public function __construct( $pathnameOrResource, $mimeType = null )
    {
        if ( is_resource( $pathnameOrResource ) &&
             in_array( get_resource_type( $pathnameOrResource ),
                       array( 'file', 'stream' ) ) )
        {
            $this->handle = $pathnameOrResource;
        }
        else if ( is_file( $pathnameOrResource ) )
        {
            $this->pathname = (string) $pathnameOrResource;
        }
        else
        {
            throw new Exception\InvalidArgumentException( sprintf(
                '%s: "%s" is not a file, neither a stream resource',
                __METHOD__,
                $pathnameOrResource
            ) );
        }

        if ( $mimeType )
        {
            $this->mimeType = (string) $mimeType;
        }
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

        if ( $this->mimeType )
        {
            return $this->mimeType;
        }

        if ( ! $this->pathname )
        {
            return 'application/octet-stream';
        }

        $this->mimeType = null;

        // @codeCoverageIgnoreStart
        if ( function_exists( 'mime_content_type' ) &&
             ini_get( 'mime_magic.magicfile' ) )
        {
            $this->mimeType = mime_content_type( $this->pathname );
        }
        // @codeCoverageIgnoreEnd

        if ( null === $this->mimeType &&
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
                $this->mimeType = finfo_file( $finfo, $this->pathname );
            }
        }

        return $this->mimeType;
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
        $this->current  = fgets( $this->handle, static::MAX_LINE_LENGTH );
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
            $line = fgets( $this->handle, static::MAX_LINE_LENGTH );

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
