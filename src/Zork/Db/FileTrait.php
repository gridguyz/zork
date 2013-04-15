<?php

namespace Zork\Db;

use Zork\Stdlib\String;

/**
 * \Zork\Db\FileTrait
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait FileTrait
{

    use SiteInfoAwareTrait;

    /**
     * Remove file form uploads
     *
     * @param string $file
     * @return self
     */
    protected function removeFile( $file )
    {
        if ( ! empty( $file ) &&
             preg_match( '#^/(uploads|tmp)/#', $file ) &&
             is_file( './public' . $file ) )
        {
            @ unlink( './public' . $file );
        }

        return $this;
    }

    /**
     * Add file to uploads
     *
     * @param string $file
     * @param string $dest evaulates in sprintf, adds a random &
     *                     an extension part to the destination
     * @return string
     */
    protected function addFile( $file, $dest )
    {
        if ( empty( $file ) )
        {
            return null;
        }

        $public = realpath( './public' );

        if ( is_file( $public . $file ) )
        {
            if ( preg_match( '#^/uploads/#', $file ) )
            {
                return $file;
            }

            if ( preg_match( '#^/tmp/#', $file ) )
            {
                $length = 8;
                $ext    = pathinfo( $public . $file, PATHINFO_EXTENSION );
                $dest   = sprintf( $dest, String::generateRandom( $length ), $ext );
                $schema = $this->getSiteInfo()->getSchema();
                $path   = '/uploads/' . $schema . '/' . $dest;

                while ( is_file( $public . $path ) )
                {
                    if ( $length > 24 )
                    {
                        @ unlink( $public . $file );
                        return null;
                    }

                    $dest   = sprintf( $dest, String::generateRandom( ++ $length ), $ext );
                    $path   = '/uploads/' . $schema . '/' . $dest;
                }

                $moveFr = $public . $file;
                $moveTo = $public . $path;
                $movDir = dirname( $moveTo );

                if ( ! is_dir( $movDir ) )
                {
                    @ mkdir( $movDir, 0777, true );
                }

                if ( @ rename( $moveFr, $moveTo ) )
                {
                    return $path;
                }
            }
        }

        return null;
    }

}
