<?php

namespace Zork\Cache;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Cache\StorageFactory;
use Zend\Cache\PatternFactory;

/**
 * CacheManager
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CacheManager
{

    /**
     * @var string
     */
    const DEFAULT_NAMESPACE     = 'default';

    /**
     * @var array
     */
    protected $storageOptions   = array();

    /**
     * @var array
     */
    protected $patternOptions   = array();

    /**
     * @var array
     */
    private $storageCache       = array();

    /**
     * @var array
     */
    private $patternCache       = array();

    /**
     * @return array
     */
    public function getStorageOptions()
    {
        return $this->storageOptions;
    }

    /**
     * @param array|\Traversable $storageOptions
     * @return \Zork\Cache\CacheManager
     */
    public function setStorageOptions( $storageOptions )
    {
        if ( $storageOptions instanceof Traversable )
        {
            $storageOptions = ArrayUtils::iteratorToArray( $storageOptions );
        }

        $this->storageOptions = $storageOptions;
        return $this;
    }

    /**
     * @return array
     */
    public function getPatternOptions()
    {
        return $this->patternOptions;
    }

    /**
     * @param array|\Traversable $patternOptions
     * @return \Zork\Cache\CacheManager
     */
    public function setPatternOptions( $patternOptions )
    {
        if ( $patternOptions instanceof Traversable )
        {
            $patternOptions = ArrayUtils::iteratorToArray( $patternOptions );
        }

        $this->patternOptions = $patternOptions;
        return $this;
    }

    /**
     * @param array|null $storageOptions
     * @param string|null $patternOptions
     */
    public function __construct( $storageOptions = null,
                                 $patternOptions = null )
    {
        if ( null !== $storageOptions )
        {
            $this->setStorageOptions( $storageOptions );
        }

        if ( null !== $patternOptions )
        {
            $this->setPatternOptions( $patternOptions );
        }
    }

    /**
     * Factory a cache-manager
     *
     * @param array $options
     * @return \Zork\Cache\CacheManager
     */
    public static function factory( array $options )
    {
        if ( isset( $options['storage'] ) )
        {
            $storageOptions = $options['storage'];
        }
        else
        {
            $storageOptions = null;
        }

        if ( isset( $options['pattern'] ) )
        {
            $patternOptions = $options['pattern'];
        }
        else
        {
            $patternOptions = null;
        }

        return new static( $storageOptions, $patternOptions );
    }

    /**
     * @param string $namespace
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function createStorage( $namespace = self::DEFAULT_NAMESPACE )
    {
        $options = array() + $this->getStorageOptions();

        if ( ! empty( $options['namespace'] ) )
        {
            $options['namespace'] = $options['namespace'] . $namespace;
        }
        else
        {
            $options['namespace'] = $namespace;
        }

        return StorageFactory::factory( $options );
    }

    /**
     * @param string $namespace
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function getStorage( $namespace = self::DEFAULT_NAMESPACE )
    {
        if ( ! isset( $this->storageCache[$namespace] ) )
        {
            $this->storageCache[$namespace] = $this->createStorage( $namespace );
        }

        return $this->storageCache[$namespace];
    }

    /**
     * @param string $namespace
     * @return \Zend\Cache\Pattern\PatternInterface
     */
    public function createPattern( $name )
    {
        $options = $this->getPatternOptions();

        if ( isset( $options[$name] ) )
        {
            $options = $options[$name];
        }
        else
        {
            $options = array();
        }

        return PatternFactory::factory( $name, $options );
    }

    /**
     * @param string $namespace
     * @return \Zend\Cache\Pattern\PatternInterface
     */
    public function getPattern( $name )
    {
        if ( ! isset( $this->patternCache[$name] ) )
        {
            $this->patternCache[$name] = $this->createPattern( $name );
        }

        return $this->patternCache[$name];
    }

}
