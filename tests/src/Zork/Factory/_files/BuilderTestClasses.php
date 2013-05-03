<?php

namespace Zork\Factory\BuilderTest;

use Countable;
use IteratorAggregate;
use Zork\Factory\AdapterAbstract;
use Zork\Factory\FactoryAbstract;

class Factory extends FactoryAbstract
{
}

abstract class Dependecy extends AdapterAbstract
{

    abstract public function method();

}

class InvalidAdapterNotAdapter implements IteratorAggregate
{

    public function getIterator()
    {
        return null;
    }

}

class InvalidAdapterNotCountable extends AdapterAbstract
{

    public static function acceptsOptions( array $options )
    {
        return false;
    }

    public function method()
    {
        return null;
    }

}

class Adapter1 extends Dependecy implements Countable
{

    public static function acceptsOptions( array $options )
    {
        return isset( $options['param1'] );
    }

    public function count()
    {
        return 1;
    }

    public function method()
    {
        return 1;
    }

}

class Adapter2 extends Dependecy implements Countable
{

    public static function acceptsOptions( array $options )
    {
        return isset( $options['param2'] );
    }

    public function count()
    {
        return 2;
    }

    public function method()
    {
        return 2;
    }

}

class AdapterForce extends Dependecy implements Countable
{

    public static function acceptsOptions( array $options )
    {
        return 10;
    }

    public function count()
    {
        return 10;
    }

    public function method()
    {
        return 10;
    }

}
