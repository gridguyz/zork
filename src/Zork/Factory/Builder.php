<?php

namespace Zork\Factory;

use ReflectionClass;
use Zend\Stdlib\ArrayUtils;

/**
 * Zork\Factory\Builder
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Builder
{

    /**
     * @var string
     */
    const ADAPTERS_INSTANCE_OF = 'Zork\Factory\AdapterInterface';

    /**
     * Registered factories
     *
     * @var array
     */
    protected $factories = array();

    /**
     * Registered adapters
     *
     * @var array
     */
    protected $adapters = array();

    /**
     * Checks if the object has this class as one of its parents
     *
     * @see https://bugs.php.net/bug.php?id=53727
     * @see https://github.com/zendframework/zf2/pull/1807
     *
     * @param string $className
     * @param $type
     * @return bool
     */
    protected static function isSubclassOf($className, $type)
    {
        if ( is_subclass_of( $className, $type ) )
        {
            return true;
        }

        if ( version_compare( PHP_VERSION, '5.3.7', '>=' ) )
        {
            return false;
        }

        if ( ! interface_exists( $type ) )
        {
            return false;
        }

        $r = new ReflectionClass( $className );
        return $r->implementsInterface( $type );
    }

    /**
     * Register a factory
     *
     * @param string $factory
     * @param array $classDepencies
     * @return \Zork\Factory\Builder
     */
    public function registerFactory( $factory, array $classDepencies = null )
    {
        $classDepencies = (array) $classDepencies;
        array_unshift( $classDepencies, static::ADAPTERS_INSTANCE_OF );
        $this->factories[$factory] = $classDepencies;
        return $this;
    }

    /**
     * Unregister a factory
     *
     * @param string $factory
     * @return \Zork\Factory\Builder
     */
    public function unregisterFactory( $factory )
    {
        unset( $this->factories[$factory] );
        unset( $this->adapters[$factory] );
        return $this;
    }

    /**
     * Register a factory-adapter
     *
     * @param string $factory
     * @param string $adapter Adapter's name
     * @param string $class Adapter's class
     * @return Zork_Factory_Builder
     * @throws \Zork\Factory\Exception\InvalidArgumentException
     */
    public function registerAdapter( $factory, $adapter, $class )
    {
        if ( ! isset( $this->factories[$factory] ) )
        {
            throw new Exception\InvalidArgumentException(
                'Factory "' . $factory . '" not registered'
            );
        }

        if ( ! class_exists( $class ) )
        {
            throw new Exception\InvalidArgumentException(
                'Factory adapter "' . $class . '" for "' .
                $factory . '" is not exists (as a class)'
            );
        }

        foreach ( $this->factories[$factory] as $instanceOf )
        {
            if ( ! static::isSubclassOf( $class, $instanceOf ) )
            {
                throw new Exception\InvalidArgumentException(
                    'Factory adapter "' . $class . '" for "' . $factory .
                    '" needs to be instance of "' . $instanceOf . '"'
                );
            }
        }

        if ( ! isset( $this->adapters[$factory] ) )
        {
            $this->adapters[$factory] = array();
        }

        $this->adapters[$factory][$adapter] = $class;
        return $this;
    }

    /**
     * Unregister a factory-adapter
     *
     * @param string $factory
     * @param string $adapter Adapter's name
     * @return \Zork\Factory\Builder
     */
    public function unregisterAdapter( $factory, $adapter )
    {
        unset( $this->adapters[$factory][$adapter] );
        return $this;
    }

    /**
     * Unregister a everything
     *
     * @return \Zork\Factory\Builder
     */
    public function unregisterAll()
    {
        $this->factories = array();
        $this->adapters = array();
        return $this;
    }

    /**
     * Is factory registered?
     *
     * @param string|\Zork\Factory\FactoryAbstract $factory
     * @return bool
     */
    public function isFactoryRegistered( $factory )
    {
        if ( is_object( $factory ) )
        {
            $factory = get_class( $factory );
        }

        return isset( $this->_factories[$factory] );
    }

    /**
     * Is adapter registered?
     *
     * @param string|\Zork\Factory\FactoryAbstract $factory
     * @param string|\Zork\Factory\AdapterInterface $adapter name or class
     * @return bool
     */
    public function isAdapterRegistered( $factory, $adapter )
    {
        if ( is_object( $factory ) )
        {
            $factory = get_class( $factory );
        }

        if ( is_object( $adapter ) )
        {
            $adapter = get_class( $adapter );
        }

        if ( isset( $this->factories[$factory] ) )
        {
            if ( class_exists( $adapter ) )
            {
                return in_array( $adapter, $this->adapters[$factory] );
            }
            else
            {
                return isset( $this->adapters[$factory][$adapter] );
            }
        }

        return false;
    }

    /**
     * Get all registered factories
     *
     * @return array of string
     */
    public function getRegisteredFactories()
    {
        return array_keys( $this->factories );
    }

    /**
     * Get all registered adapters (by name => class) for a factory
     *
     * @param string|\Zork\Factory\FactoryAbstract $factory
     * @return array
     */
    public function getRegisteredAdapters( $factory )
    {
        if ( is_object( $factory ) )
        {
            $factory = get_class( $factory );
        }

        if ( isset( $this->factories[$factory] ) )
        {
            return $this->adapters[$factory];
        }

        return null;
    }

    /**
     * Build a factory-class-instance
     *
     * @param string $factory class-name
     * @param string|object|array $adapter
     * @param object|array|null $options
     * @return \Zork\Factory\AdapterInterface
     */
    public function build( $factory, $adapter, $options = null )
    {
        if ( ! isset( $this->factories[$factory] ) ||
               empty( $this->adapters[$factory] ) )
        {
            return null;
        }

        if ( $adapter instanceof \Traversable )
        {
            $adapter = ArrayUtils::iteratorToArray( $adapter );
        }

        if ( $options instanceof \Traversable )
        {
            $options = ArrayUtils::iteratorToArray( $options );
        }

        if ( is_array( $adapter ) )
        {
            if ( is_array( $options ) )
            {
                $adapter = ArrayUtils::merge( $adapter, $options );
            }

            $options = $adapter;
            $adapter = null;
            $max     = 0;

            if ( isset( $options['adapter'] ) )
            {
                $adapter = $options['adapter'];
                unset( $options['adapter'] );
            }
            else
            {
                foreach ( $this->adapters[$factory] as $name => $class )
                {
                    $current = (float) $class::acceptsOptions( $options );

                    if ( $current > $max )
                    {
                        $adapter = $name;
                        $max     = $current;
                    }
                }
            }
        }
        else
        {
            $adapter = (string) $adapter;
            $options = (array) $options;
        }

        if ( ! isset( $this->adapters[$factory][$adapter] ) )
        {
            return null;
        }

        $class = $this->adapters[$factory][$adapter];
        return $class::factory( $options );
    }

    /**
     * Build a factory-builder from options
     *
     * @param array $factories
     * @return \Zork\Factory\Builder
     */
    public static function factory( array $factories )
    {
        $builder = new static();

        foreach ( $factories as $factory => $spec )
        {
            $spec = (array) $spec;

            if ( array_key_exists( 'dependency', $spec ) )
            {
                $builder->registerFactory( $factory, (array) $spec['dependency'] );

                foreach ( (array) $spec['adapter'] as $adapter => $class )
                {
                    $builder->registerAdapter( $factory, $adapter, $class );
                }
            }
        }

        return $builder;
    }

}
