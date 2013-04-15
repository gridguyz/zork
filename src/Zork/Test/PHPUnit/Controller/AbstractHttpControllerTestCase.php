<?php

namespace Zork\Test\PHPUnit\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase as BaseTestCase;

/**
 * AbstractHttpControllerTestCase
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AbstractHttpControllerTestCase extends BaseTestCase
{

    /**
     * Application config path
     *
     * @var string
     */
    protected $applicationConfigPath = 'config/application.config.php';

    /**
     * Set config & reset the application for isolation
     */
    public function setUp()
    {
        $this->setApplicationConfig( include $this->applicationConfigPath );
        parent::setUp();
    }

}
