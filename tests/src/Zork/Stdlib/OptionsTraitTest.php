<?php

namespace Zork\Stdlib;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * OptionsTraitTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Stdlib\OptionsTrait
 */
class OptionsTraitTest extends TestCase
{

    /**
     * @var OptionsTraitTestClass
     */
    protected $test;

    /**
     * This method is called before the first test of this test class is run
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        include_once __DIR__ . '/_files/OptionsTraitTestClass.php';
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        parent::setUp();

        $this->test = new OptionsTraitTestClass();
    }

    /**
     * @covers Zork\Stdlib\OptionsTrait::getOptions
     * @covers Zork\Stdlib\OptionsTrait::getOption
     */
    public function testSetValidProperties()
    {
        $props = array(
            'publicProperty'    => 'foo',
            'protectedProperty' => 'bar',
            'privateProperty'   => 'baz',
        );

        $this->test->setOptions( $props );

        $this->assertEquals( (object) $props, $this->test->getOptions() );
        $this->assertAttributeEquals( 'foo', 'publicProperty', $this->test );
        $this->assertAttributeEquals( 'bar', 'protectedProperty', $this->test );
        $this->assertAttributeEquals( 'baz', 'privateProperty', $this->test );
        $this->assertEquals( 'foo', $this->test->getOption( 'publicProperty' ) );
        $this->assertEquals( 'bar', $this->test->getOption( 'protectedProperty' ) );
        $this->assertEquals( 'baz', $this->test->getOption( 'privateProperty' ) );
    }

    /**
     * @covers Zork\Stdlib\OptionsTrait::setOptions
     */
    public function testSetters()
    {
        $props = array(
            'publicPropertyWithSetter'      => 'foo',
            'protectedPropertyWithSetter'   => 'bar',
            'privatePropertyWithSetter'     => 'baz',
        );

        $this->test->setOptions( $props );

        $this->assertEquals( (object) $props, $this->test->getOptions() );
        $this->assertAttributeEquals( 'foo', 'publicProperty', $this->test );
        $this->assertAttributeEquals( 'bar', 'protectedProperty', $this->test );
        $this->assertAttributeEquals( 'baz', 'privateProperty', $this->test );
        $this->assertEquals( 'foo', $this->test->getOption( 'publicPropertyWithSetter' ) );
        $this->assertEquals( 'bar', $this->test->getOption( 'protectedPropertyWithSetter' ) );
        $this->assertEquals( 'baz', $this->test->getOption( 'privatePropertyWithSetter' ) );
    }

    /**
     * @covers Zork\Stdlib\OptionsTrait::setOption
     */
    public function testHiddenProperties()
    {
        $props = array(
            '_hiddenPublicProperty'     => 'foo',
            '_hiddenProtectedProperty'  => 'bar',
            '_hiddenPrivateProperty'    => 'baz',
        );

        $this->test->setOptions( $props );

        $this->assertEquals( (object) $props, $this->test->getOptions() );
        $this->assertAttributeEmpty( '_hiddenPublicProperty', $this->test );
        $this->assertAttributeEmpty( '_hiddenProtectedProperty', $this->test );
        $this->assertAttributeEmpty( '_hiddenPrivateProperty', $this->test );
        $this->assertEquals( 'foo', $this->test->getOption( '_hiddenPublicProperty' ) );
        $this->assertEquals( 'bar', $this->test->getOption( '_hiddenProtectedProperty' ) );
        $this->assertEquals( 'baz', $this->test->getOption( '_hiddenPrivateProperty' ) );
    }

}
