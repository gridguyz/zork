<?php

namespace Zork\Stdlib;

use InvalidArgumentException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * FileSystem
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FileSystem
{

    /**
     * Clear old files
     *
     * @param   string  $path
     * @param   int     $ttl
     * @param   bool    $recursive
     * @param   bool    $keepDirectories
     * @return  int
     */
    public static function clearOldFiles( $path,
                                          $ttl              = 18000,
                                          $recursive        = true,
                                          $keepDirectories  = true )
    {
        $path   = (string) $path;
        $ttl    = abs( (int) $ttl );

        if ( ! is_dir( $path ) )
        {
            throw new InvalidArgumentException( sprintf(
                '%s: path "%s" is not a directory',
                __METHOD__,
                $path
            ) );
        }

        $iterator = new RecursiveDirectoryIterator(
            $path,
            RecursiveDirectoryIterator::SKIP_DOTS
        );

        if ( $recursive )
        {
            $iterator = new RecursiveIteratorIterator(
                $iterator,
                RecursiveIteratorIterator::CHILD_FIRST
            );
        }

        $all = 0;
        $now = time();

        foreach ( $iterator as $entry )
        {
            /* @var $entry \SplFileInfo */

            if ( ! $entry->isWritable() ||
                 ( $keepDirectories && $entry->isDir() ) ||
                 ( substr( $entry->getFilename(), 0, 1 ) == '.' ) )
            {
                continue;
            }

            if ( ( $now - $entry->getMTime() ) > $ttl )
            {
                $pathname = $entry->getPathname();

                if ( $entry->isDir() )
                {
                    rmdir( $pathname );
                }
                else
                {
                    unlink( $pathname );
                }

                $all++;
            }
        }

        return $all;
    }

}
