<?php

namespace Zork\Stdlib\Hydrator;

use ArrayObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * ArrayAccessTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Stdlib\Hydrator\ArrayAccess
 */
class ArrayAccessTest extends TestCase
{

    /**
     * @var array
     */
    public $data = array(
        'foo' => M_PI,
        'bar' => INF,
        'baz' => null,
    );

    /**
     * @var ArrayAccess
     */
    protected $hydrator;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->hydrator = new ArrayAccess();
    }

    /**
     * Test extract
     */
    public function testExtract()
    {
        $this->assertSame(
            $this->data,
            $this->hydrator->extract( $this->data )
        );

        $this->assertSame(
            $this->data,
            $this->hydrator->extract( new ArrayObject( $this->data ) )
        );
    }

    /**
     * Test hydrate
     */
    public function testHydrate()
    {
        $object = new ArrayObject();
        $this->hydrator->hydrate( $this->data, $object );
        $this->assertSame( $this->data, $object->getArrayCopy() );
    }

}
