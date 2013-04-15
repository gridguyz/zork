<?php

namespace Zork\Permissions\Acl;

use Zend\Permissions\Acl\Acl;

/**
 * Zork_Model_MapperAggregateInterface for models
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface AclAwareInterface
{
    
    /**
     * Get the mapper object
     * 
     * @return \Zend\Permissions\Acl\Acl
     */
    public function getAcl();
    
    /**
     * Set the mapper object
     * 
     * @param \Zend\Permissions\Acl\Acl $acl
     * @return \Zork\Permissions\Acl\AclAwareInterface
     */
    public function setAcl( Acl $acl );
    
}
