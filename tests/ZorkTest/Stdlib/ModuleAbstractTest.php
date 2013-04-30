<?php

namespace ZorkTest\Stdlib;

use TestModule\Module;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * ModuleAbstractTest
 *
 * @author pozs
 * @covers Zork\ModuleAbstract
 */
class ModuleAbstractTest extends TestCase
{

    /**
     * @var \TestModule\Module
     */
    protected $module;

    /**
     * This method is called before the first test of this test class is run
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        include_once __DIR__ . '/_files/Module.php';
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        parent::setUp();

        $this->module = new Module();
    }

    /**
     * @covers Zork\Stdlib\ModuleAbstract::getConfig
     */
    public function testConfig()
    {
        $this->assertEquals(
            array( 'foo' => 'bar' ),
            $this->module->getConfig()
        );
    }

}
