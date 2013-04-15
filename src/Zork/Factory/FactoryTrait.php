<?php

namespace Zork\Factory;

/**
 * \Zork\Factory\FactoryTrait
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait FactoryTrait
{

    /**
     * Stored builder
     *
     * @var \Zork\Factory\Builder
     */
    protected $builder;

    /**
     * Get factory-builder
     *
     * @return \Zork\Factory\Builder
     */
    public function getFactoryBuilder()
    {
        return $this->builder;
    }

    /**
     * Set factory-builder
     *
     * @param \Zork\Factory\Builder $factoryBuilder
     * @return self
     */
    public function setFactoryBuilder( Builder $factoryBuilder )
    {
        $this->builder = $factoryBuilder;
        return $this;
    }

    /**
     * Constructor
     *
     * @param \Zork\Factory\Builder $factoryBuilder
     */
    public function __construct( Builder $factoryBuilder )
    {
        $this->builder = $factoryBuilder;
    }

    /**
     * Register this factory
     *
     * @param array $classDepencies
     * @return self
     */
    public function registerFactory( array $classDepencies = array() )
    {
        $this->builder
             ->registerFactory(
                    get_called_class(),
                    $classDepencies
                );

        return $this;
    }

    /**
     * Unregister this factory
     *
     * @return self
     */
    public function unregisterFactory()
    {
        $this->builder
             ->unregisterFactory(
                    get_called_class()
                );

        return $this;
    }

    /**
     * Register a factory-adapter
     *
     * @param string $adapter Adapter's name
     * @param string $class Adapter's class
     * @return self
     */
    public function registerAdapter( $adapter, $class )
    {
        $this->builder
             ->registerAdapter(
                    get_called_class(),
                    $adapter,
                    $class
                );

        return $this;
    }

    /**
     * Unregister a factory-adapter
     *
     * @param string $adapter Adapter's name
     * @return self
     */
    public function unregisterAdapter( $adapter )
    {
        $this->builder
             ->unregisterAdapter(
                    get_called_class(),
                    $adapter
                );

        return $this;
    }

    /**
     * Factory an object
     *
     * @param string|object|array $adapter
     * @param object|array|null $options
     * @return \Zork\Factory\AdapterInterface
     */
    public function factory( $adapter, $options = null )
    {
        return $this->builder
                    ->build(
                        get_called_class(),
                        $adapter,
                        $options
                    );
    }

    /**
     * Is this factory registered?
     *
     * @return bool
     */
    public function isFactoryRegistered()
    {
        return $this->builder
                    ->isFactoryRegistered(
                        get_called_class()
                    );
    }

    /**
     * Is adapter registered?
     *
     * @param string|Zork_Factory_AdapterInterface $adapter name or class
     * @return bool
     */
    public function isAdapterRegistered( $adapter )
    {
        return $this->builder
                    ->isAdapterRegistered(
                        get_called_class(),
                        $adapter
                    );
    }

    /**
     * Get all registered adapters (by name => class) for a factory
     *
     * @return array
     */
    public function getRegisteredAdapters()
    {
        return $this->builder
                    ->getRegisteredAdapters(
                        get_called_class()
                    );
    }

}
