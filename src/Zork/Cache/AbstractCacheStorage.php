<?php

namespace Zork\Cache;

use Zend\Cache\Storage\StorageInterface;

/**
 * AbstractCache
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AbstractCacheStorage
{

    /**
     * @var \Zend\Cache\Storage\StorageInterface
     */
    protected $cacheStorage;

    /**
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function getCacheStorage()
    {
        return $this->cacheStorage;
    }

    /**
     * @param \Zend\Cache\Storage\StorageInterface $cacheAdapter
     * @return \Zork\Cache\AbstractCacheStorage
     */
    public function setCacheStorage( StorageInterface $cacheAdapter )
    {
        $this->cacheStorage = $cacheAdapter;
        return $this;
    }

    /**
     * @param \Zork\Cache\CacheManager $cacheManager
     * @param string $namespace
     */
    public function __construct( CacheManager $cacheManager, $namespace = null )
    {
        $this->setCacheStorage(
            $cacheManager->createStorage( $namespace ?: get_called_class() )
        );
    }

}
