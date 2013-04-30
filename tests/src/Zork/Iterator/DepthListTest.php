<?php

namespace Zork\Iterator;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * DepthListTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DepthListTest extends TestCase
{

    public $list = array(
        array( 1, '1' ),
        array( 2, '1.1' ),
        array( 2, '1.2' ),
        array( 3, '1.2.1' ),
        array( 3, '1.2.2' ),
        array( 2, '1.3' ),
        array( 3, '1.3.1' ),
        array( 4, '1.3.1.1' ),
        array( 2, '1.4' ),
    );

    /**
     * @var DepthList
     */
    protected $depthList;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->depthList = new DepthList( $this->list );
    }

    /**
     * Test stack
     */
    public function testStack()
    {
        $self  = $this;
        $stack = array();

        $this->depthList->runin(
            function ( $value ) use ( &$stack ) {
                $stack[] = $value;
            },
            function ( $value ) use ( &$stack, $self ) {
                $self->assertEquals( array_pop( $stack ), $value );
            }
        );

        $this->assertEmpty( $stack, 'Stack should be empty after runin()' );
    }

}
