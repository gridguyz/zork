<?php

namespace Zork\Db;

use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * DbAwareServices
 *
 * @author pozs
 */
interface SiteConfigurationInterface extends ServiceLocatorAwareInterface
{

    /**
     * Setup services which depends on the db
     *
     * @param   \Zend\Db\Adapter\Adapter $db
     * @return  \Zend\Db\Adapter\Adapter
     */
    public function configure( DbAdapter $db );

}
