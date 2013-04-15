<?php

namespace Zork\Model;

use Zend\Db\Adapter\Adapter;

/**
 * DbAdapterAwareInterface
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface DbAdapterAwareInterface
{
    
    /**
     * Get db-adapter instance
     * 
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getDbAdapter();
    
    /**
     * Set db-adapter instance
     * 
     * @param \Zend\Db\Adapter\Adapter $db
     * @return DbAdapterAwareInterface
     */
    public function setDbAdapter( Adapter $dbAdapter );
    
}
