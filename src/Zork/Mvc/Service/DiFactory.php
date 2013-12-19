<?php

namespace Zork\Mvc\Service;

use Zend\Di\Di;
use Zend\Di\Config;
use Zend\Di\DefinitionList;
use Zend\Cache\StorageFactory;
use Zend\Mvc\Service\DiFactory as ZendDiFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zork\Di\Definition\CachedRuntimeDefinition;

/**
 * Dependecy injector facory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DiFactory extends ZendDiFactory
{

    /**
     * @const string
     */
    const DEFAULT_CACHE_NAMESPACE = 'Zork\Di\Definition\CachedRuntimeDefinition';

    /**
     * {@inheritDoc}
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        $config = $serviceLocator->get( 'Config' );

        if ( empty( $config['di']['cache'] ) )
        {
            return parent::createService( $serviceLocator );
        }

        $definition = new CachedRuntimeDefinition();

        foreach ( (array) $config['di']['cache'] as $name => $value )
        {
            switch ( strtolower( $name ) )
            {
                case 'mincount':
                case 'min_count':
                case 'cachemincount':
                case 'cache_min_count':
                    $definition->setCacheMinCount( $value );
                    break;

                case 'minhitrate':
                case 'min_hitrate':
                case 'cacheminhitrate':
                case 'cache_min_hitrate':
                    $definition->setCacheMinHitrate( $value );
                    break;

                case 'storage':
                case 'cachestorage':
                case 'cache_storage':
                    $value = (array) $value;

                    if ( empty( $value['adapter']['options']['namespace'] ) )
                    {
                        $namespace = static::DEFAULT_CACHE_NAMESPACE;
                    }
                    else
                    {
                        $namespace = $value['adapter']['options']['namespace'];
                    }

                    $namespace = trim(
                        preg_replace(
                            array( '#\\\\+#', '#[^a-z0-9_-]+#' ),
                            array( '-', '_' ),
                            strtolower( $namespace )
                        ),
                        '-'
                    );

                    $value['adapter']['options']['namespace'] = $namespace;
                    $storage = StorageFactory::factory( $value );
                    $definition->setCacheStorage( $storage );
                    break;
            }
        }

        $di         = new Di( new DefinitionList( $definition ) );
        $diConfig   = new Config( $config['di'] );
        $diConfig->configure( $di );
        return $di;
    }

}
