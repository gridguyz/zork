<?php

namespace Zork\Model\Mapper;

/**
 * Zork_Model_Mapper_ReadWriteInterface
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface ReadWriteMapperInterface
  extends ReadOnlyMapperInterface
{
    
    /**
     * Create a structure
     * 
     * @param array|\Traversable $data
     * @return \Zork\Model\Structure\StructureAbstract
     */
    public function create( $data );
    
    /**
     * Save a structure
     * 
     * @param array|\Zork\Model\Structure\StructureAbstract $structure
     * @return int
     */
    public function save( & $structure );
    
    /**
     * Remove a structure
     * 
     * @param int|string|array|\Zork\Model\Structure\StructureAbstract $structureOrPrimaryKeys
     * @return int
     */
    public function delete( $structureOrPrimaryKeys );
    
}
