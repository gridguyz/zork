<?php

namespace ZorkTest\Stdlib;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * PropertiesTraitTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Stdlib\PropertiesTrait
 */
class PropertiesTraitTest extends TestCase
{

    /**
     * @var PropertiesTraitTestClass
     */
    protected $test;

    /**
     * This method is called before the first test of this test class is run
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        include_once __DIR__ . '/_files/PropertiesTraitTestClass.php';
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        parent::setUp();

        $this->test = new PropertiesTraitTestClass();
    }

    /**
     * @covers Zork\Stdlib\PropertiesTrait::__set
     * @covers Zork\Stdlib\PropertiesTrait::__get
     */
    public function testSetGetProperties()
    {
        $this->test->publicPropertyWithSetter       = 'foo';
        $this->test->protectedPropertyWithSetter    = 'bar';
        $this->test->privatePropertyWithSetter      = 'baz';

        $this->assertAttributeEquals( 'foo', 'publicProperty', $this->test );
        $this->assertAttributeEquals( 'bar', 'protectedProperty', $this->test );
        $this->assertAttributeEquals( 'baz', 'privateProperty', $this->test );
        $this->assertEquals( 'foo', $this->test->publicProperty );
        $this->assertEquals( 'bar', $this->test->protectedProperty );
        $this->assertEquals( 'baz', $this->test->privateProperty );
        $this->assertEquals( 'foo', $this->test->publicPropertyWithGetter );
        $this->assertEquals( 'bar', $this->test->protectedPropertyWithGetter );
        $this->assertEquals( 'baz', $this->test->privatePropertyWithGetter );
        $this->assertNull( $this->test->nonExistentProperty );
    }

    /**
     * @covers Zork\Stdlib\PropertiesTrait::__isset
     * @covers Zork\Stdlib\PropertiesTrait::__unset
     * @covers Zork\Stdlib\PropertiesTrait::offsetExists
     * @covers Zork\Stdlib\PropertiesTrait::offsetUnset
     */
    public function testIssetUnsetProperties()
    {
        $this->test->publicPropertyWithSetter       = 'foo';
        $this->test->protectedPropertyWithSetter    = 'bar';
        $this->test->privatePropertyWithSetter      = 'baz';

        $this->assertTrue( isset( $this->test->publicPropertyWithGetter ) );
        $this->assertTrue( isset( $this->test->protectedPropertyWithGetter ) );
        $this->assertTrue( isset( $this->test->privatePropertyWithGetter ) );
        $this->assertFalse( isset( $this->test->nonExistentProperty ) );

        unset( $this->test->publicPropertyWithSetter );
        unset( $this->test->protectedPropertyWithSetter );
        unset( $this->test->privatePropertyWithSetter );

        $this->assertFalse( isset( $this->test->publicPropertyWithGetter ) );
        $this->assertFalse( isset( $this->test->protectedPropertyWithGetter ) );
        $this->assertFalse( isset( $this->test->privatePropertyWithGetter ) );

        $this->test->publicPropertyWithSetter       = 'foo';
        $this->test->protectedPropertyWithSetter    = 'bar';
        $this->test->privatePropertyWithSetter      = 'baz';

        $this->assertTrue( isset( $this->test->publicPropertyWithIssetter ) );
        $this->assertTrue( isset( $this->test->protectedPropertyWithIssetter ) );
        $this->assertTrue( isset( $this->test->privatePropertyWithIssetter ) );

        unset( $this->test->publicPropertyWithUnsetter );
        unset( $this->test->protectedPropertyWithUnsetter );
        unset( $this->test->privatePropertyWithUnsetter );

        $this->assertFalse( isset( $this->test->publicPropertyWithIssetter ) );
        $this->assertFalse( isset( $this->test->protectedPropertyWithIssetter ) );
        $this->assertFalse( isset( $this->test->privatePropertyWithIssetter ) );
    }

    /**
     * @covers Zork\Stdlib\PropertiesTrait::offsetSet
     * @expectedException           LogicException
     * @expectedExceptionMessage    Read-only
     */
    public function testSetHiddenProperties()
    {
        $this->test->_hiddenPrivateProperty = 'baz';
    }

    /**
     * @covers Zork\Stdlib\PropertiesTrait::offsetGet
     * @expectedException           LogicException
     * @expectedExceptionMessage    not accessible
     */
    public function testGetHiddenProperties()
    {
        $tmp = $this->test->_hiddenProtectedProperty;
    }

}
