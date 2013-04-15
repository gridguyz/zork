<?php

namespace Zork\Permissions\Acl;

use Zend\Permissions\Acl\Acl;

/**
 * AclAwareTrait for models
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @implements \Zork\Permissions\Acl\AclAwareInterface
 */
trait AclAwareTrait
{

    /**
     * Acl instance
     *
     * @var \Zend\Permissions\Acl\Acl
     */
    private $acl;

    /**
     * Get the mapper object
     *
     * @return \Zend\Permissions\Acl\Acl
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * Set the mapper object
     *
     * @param \Zend\Permissions\Acl\Acl $acl
     * @return \Zork\Permissions\Acl\AclAwareInterface
     */
    public function setAcl( Acl $acl )
    {
        $this->acl = $acl;
        return $this;
    }

}
