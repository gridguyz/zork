<?php

namespace Zork\Model\Mapper\DbAware;

/**
 * SchemaAwareInterface
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface DbSchemaAwareInterface
{
    
    /**
     * Get schema
     * 
     * @return string
     */
    public function getDbSchema();
    
    /**
     * Set schema
     * 
     * @param string $dbSchema
     * @return Zork\Model\Mapper\DbAware\DbSchemaAwareInterface
     */
    public function setDbSchema( $dbSchema = null );
    
}
