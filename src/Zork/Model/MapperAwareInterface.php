<?php

namespace Zork\Model;

/**
 * Zork_Model_MapperAggregateInterface for models
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface MapperAwareInterface
{
    
    /**
     * Get the mapper object
     * 
     * @return \Zork\Model\Mapper\ReadOnlyMapperInterface
     */
    public function getMapper();
    
    /**
     * Set the mapper object
     * 
     * @param \Zork\Model\Mapper\ReadOnlyMapperInterface $mapper
     * @return \Zork\Model\MapperAwareInterface
     */
    public function setMapper( $mapper = null );
    
}
