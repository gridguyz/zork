<?php

namespace Zork\Test\PHPUnit\Controller;

use Zork\Test\PHPUnit\TestCaseTrait;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase as BaseTestCase;

/**
 * AbstractHttpControllerTestCase
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AbstractHttpControllerTestCase extends BaseTestCase
{

    use TestCaseTrait;

    /**
     * Application config path
     *
     * @var string
     */
    protected $applicationConfigPath = 'config/application.php';

    /**
     * Set config & reset the application for isolation
     */
    public function setUp()
    {
        $this->setApplicationConfig( include $this->applicationConfigPath );
        parent::setUp();
    }

    /**
     * Get a service by its name
     *
     * @param   string  $name
     * @return  mixed
     */
    public function getService( $name )
    {
        return $this->getApplication()
                    ->getServiceManager()
                    ->get( $name );
    }

}
