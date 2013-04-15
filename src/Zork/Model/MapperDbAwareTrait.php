<?php

namespace Zork\Model;

use Zend\Db\Adapter\Adapter;

/**
 * MapperDbAwareTrait for models
 *
 * implements DbAdapterAwareInterface,
 *            MapperAwareInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait MapperDbAwareTrait
{

    use MapperAwareTrait;

    /**
     * Get db-adapter instance
     *
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getDbAdapter()
    {
        return $this->getMapper()
                    ->getDbAdapter();
    }

    /**
     * Set db-adapter instance
     *
     * @param \Zend\Db\Adapter\Adapter $db
     * @return DbAdapterAwareInterface
     */
    public function setDbAdapter( Adapter $dbAdapter )
    {
        $this->getMapper()
             ->setDbAdapter( $dbAdapter );

        return $this;
    }

}
