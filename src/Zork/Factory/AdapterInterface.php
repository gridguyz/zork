<?php

namespace Zork\Factory;

/**
 * \Zork\Factory\AdapterInterface
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface AdapterInterface
{
    
    /**
     * Return true if and only if $options accepted by this adapter
     * If returns float as likelyhood the max of these will be used as adapter
     * 
     * @param array $options;
     * @return float
     */
    public static function acceptsOptions( array $options );
    
    /**
     * Return a new instance of the adapter by $options
     * 
     * @param array $options;
     * @return Zork\Factory\AdapterInterface
     */
    public static function factory( array $options = null );
    
}
