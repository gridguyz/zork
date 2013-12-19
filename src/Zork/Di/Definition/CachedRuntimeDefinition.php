<?php

namespace Zork\Di\Definition;

use Zend\Cache\Storage\StorageInterface;
use Zend\Di\Definition\RuntimeDefinition;

/**
 * CachedRuntimeDefinition
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CachedRuntimeDefinition extends RuntimeDefinition
{

    /**
     * @const int
     */
    const DEFAULT_CACHE_MIN_COUNT = 20;

    /**
     * @const float
     */
    const DEFAULT_CACHE_MIN_HITRATE = 0.9;

    /**
     * @var int
     */
    protected $cacheMinCount = self::DEFAULT_CACHE_MIN_COUNT;

    /**
     * @var float
     */
    protected $cacheMinHitrate = self::DEFAULT_CACHE_MIN_HITRATE;

    /**
     * @var StorageInterface
     */
    private $cacheStorage;

    /**
     * @var bool
     */
    private $cacheStorageInitialized;

    /**
     * @var int
     */
    private $cacheStorageCount;

    /**
     * @return  int
     */
    public function getCacheMinCount()
    {
        return $this->cacheMinCount;
    }

    /**
     * @return  float
     */
    public function getCacheMinHitrate()
    {
        return $this->cacheMinHitrate;
    }

    /**
     * @param   int     $cacheMinCount
     * @return  CachedRuntimeDefinition
     */
    public function setCacheMinCount( $cacheMinCount )
    {
        $this->cacheMinCount = abs( (int) $cacheMinCount ) ?: 0;
        return $this;
    }

    /**
     * @param   float   $cacheMinHitrate
     * @return  CachedRuntimeDefinition
     */
    public function setCacheMinHitrate( $cacheMinHitrate )
    {
        $this->cacheMinHitrate = abs( (float) $cacheMinHitrate ) ?: 0;
        return $this;
    }

    /**
     * @return  StorageInterface
     */
    public function getCacheStorage()
    {
        return $this->cacheStorage;
    }

    /**
     * @param   StorageInterface    $cacheStorage
     * @return  CachedRuntimeDefinition
     */
    public function setCacheStorage( StorageInterface $cacheStorage )
    {
        $this->cacheStorageInitialized = false;
        $this->cacheStorage = $cacheStorage;
        return $this;
    }

    /**
     * @return  StorageInterface|false
     */
    protected function getCacheStorageAvailable()
    {
        $storage = $this->getCacheStorage();

        if ( ! $storage )
        {
            return false;
        }

        if ( $this->cacheStorageInitialized )
        {
            return $storage;
        }

        $this->cacheStorageCount = $storage->incrementItem( 'count', 1 ) ?: 1;
        $this->cacheStorageInitialized = true;
        return $storage;
    }

    /**
     * Get real class name
     *
     * @staticvar   array   $map
     * @param       string  $classLower
     * @return      string
     */
    private function getRealClassName( $classLower )
    {
        static $cache = array();

        if ( isset( $cache[$classLower] ) )
        {
            return $cache[$classLower];
        }

        $reflection = new \ReflectionClass( $classLower );
        return $cache[$classLower] = $reflection->getName();
    }

    /**
     * {@inheritDoc}
     */
    protected function processClass( $class )
    {
        static $jsonFlags = null;
        $classLower = strtolower( $class );
        $className  = $this->getRealClassName( $classLower );
        $storage    = $this->getCacheStorageAvailable();

        if ( $storage )
        {
            $success    = false;
            $count      = $this->cacheStorageCount;
            $key        = str_replace( '\\', '-', $classLower );
            $hit        = $storage->incrementItem( 'hit-' . $key, 1 ) ?: 0;
            $definition = $storage->getItem( 'def-' . $key, $success );

            if ( $success )
            {
                $this->classes[$className] = json_decode( $definition, true );
                return;
            }
        }

        parent::processClass( $className );

        if ( $storage && $this->getCacheMinCount() <= $count
                      && $this->getCacheMinHitrate() <= ( $hit / $count ) )
        {
            if ( null === $jsonFlags )
            {
                $jsonFlags = 0;

                if ( defined( 'JSON_UNESCAPED_SLASHES' ) )
                {
                    $jsonFlags |= JSON_UNESCAPED_SLASHES;
                }

                if ( defined( 'JSON_UNESCAPED_UNICODE' ) )
                {
                    $jsonFlags |= JSON_UNESCAPED_UNICODE;
                }
            }

            $storage->setItem(
                'def-' . $key,
                json_encode( $this->classes[$className], $jsonFlags )
            );
        }
    }

}
