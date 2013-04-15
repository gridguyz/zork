<?php

namespace Zork\Model;

use Zend\Db\Adapter\Adapter;

/**
 * DbAdapterAwareTrait
 * 
 * implements Zork\Model\DbAdapterAwareInterface
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait DbAdapterAwareTrait
{
    
    /**
     * Db-adapter instance
     * 
     * @var \Zend\Db\Adapter\Adapter
     */
    private $dbAdapter = null;
    
    /**
     * Get db-adapter instance
     * 
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }
    
    /**
     * Set db-adapter instance
     * 
     * @param \Zend\Db\Adapter\Adapter $db
     * @return DbAdapterAwareInterface
     */
    public function setDbAdapter( Adapter $dbAdapter )
    {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }
    
}
