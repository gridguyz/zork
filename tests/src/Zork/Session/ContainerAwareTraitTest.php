<?php

namespace Zork\Session;

use Zend\Session\SessionManager;
use Zend\Session\Storage\ArrayStorage;
use Zend\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;
use Zork\Session\ContainerAwareTraitTest\ContainerAwareSample;

/**
 * ContainerAwareTraitTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ContainerAwareTraitTest extends TestCase
{

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var SessionManager
     */
    protected $sessionManager;

    /**
     * @var ContainerAwareSample
     */
    protected $containerAwareSample;

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        include_once __DIR__ . '/_files/ContainerAwareTraitTestClasses.php';
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->serviceManager = new ServiceManager;
        $this->serviceManager
             ->setService(
                    'Zend\Session\ManagerInterface',
                    $this->sessionManager = new SessionManager( null, new ArrayStorage )
                );

        $this->containerAwareSample = new ContainerAwareSample;
        $this->containerAwareSample
             ->setServiceLocator( $this->serviceManager );
    }

    /**
     * Test get SessionManager
     */
    public function testGetSessionManager()
    {
        $this->assertSame(
            $this->sessionManager,
            $this->containerAwareSample
                 ->getSessionManager()
        );
    }

    /**
     * Test get SessionContainer
     */
    public function testGetSessionContainer()
    {
        $container = $this->containerAwareSample
                          ->container();

        $this->assertEquals(
            get_class( $this->containerAwareSample ),
            $container->getName()
        );
    }

}
