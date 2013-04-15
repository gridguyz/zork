<?php

namespace Zork\Factory;

use Zork\Stdlib\OptionsTrait;

/**
 * \Zork\Factory\AdapterAbstract
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AdapterAbstract
    implements AdapterInterface
{

    use OptionsTrait;

    /**
     * Return a new instance of the adapter by $options
     *
     * @param array $options;
     * @return Zork\Factory\AdapterAbstract
     */
    public static function factory( array $options = null )
    {
        $adapter = new static();
        $adapter->setOptions( $options );
        return $adapter;
    }

}
