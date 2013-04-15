<?php

namespace Zork\Model;

/**
 * Zork_Model_MapperAggregateInterface for models
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface ModelAwareInterface
{
    
    /**
     * Get the mapper object
     * 
     * @return \Zork\Model\Mapper\ReadOnlyMapperInterface
     */
    public function getModel();
    
    /**
     * Set the mapper object
     * 
     * @param mixed $model
     * @return \Zork\Model\MapperAwareInterface
     */
    public function setModel( $model = null );
    
}
