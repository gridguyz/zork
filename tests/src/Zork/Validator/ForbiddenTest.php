<?php

namespace Zork\Validator;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * ForbiddenTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Validator\Forbidden
 */
class ForbiddenTest extends TestCase
{

    /**
     * Test Haystack left out
     *
     * @expectedException   RuntimeException
     */
    public function testEmptyHaystack()
    {
        $validator = new Forbidden;
        $validator->getHaystack();
    }

    /**
     * Test default
     */
    public function testDefault()
    {
        $validator = new Forbidden( array(
            'haystack' => array( 'foo', 'bar', 42, '43foo' ),
        ) );

        $this->assertFalse( $validator->getRecursive() );
        $this->assertSame( array( 'foo', 'bar', 42, '43foo' ), $validator->getHaystack() );

        $this->assertFalse( $validator->isValid( 'foo' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 'bar' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 'baz' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '0' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 0 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 42 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 43 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '43' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '42foo' ), implode( PHP_EOL, $validator->getMessages() ) );

        $this->setExpectedException( 'InvalidArgumentException' );
        $validator->setStrict( PHP_INT_MAX );
    }

    /**
     * Test strict
     */
    public function testStrict()
    {
        $validator = new Forbidden( array(
            'haystack'  => array( 1, '2', 'foo' ),
            'strict'    => Forbidden::COMPARE_STRICT,
        ) );

        $this->assertEquals( Forbidden::COMPARE_STRICT, $validator->getStrict() );

        $this->assertFalse( $validator->isValid( 1 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( '2' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 'foo' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 0 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '0' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '1' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 2 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 'bar' ), implode( PHP_EOL, $validator->getMessages() ) );
    }

    /**
     * Test not strict
     */
    public function testNotStrict()
    {
        $validator = new Forbidden( array(
            'haystack'  => array( 1, '2', 'foo' ),
            'strict'    => Forbidden::COMPARE_NOT_STRICT,
        ) );

        $this->assertEquals( Forbidden::COMPARE_NOT_STRICT, $validator->getStrict() );

        $this->assertFalse( $validator->isValid( 1 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( '2' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 'foo' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( '1' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 2 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 0 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '0' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 'bar' ), implode( PHP_EOL, $validator->getMessages() ) );
    }

    /**
     * Test recursive
     */
    public function testRecursive()
    {
        $validator = new Forbidden( array(
            'haystack'  => array( 'foo', 'baz' => array( 'bar', 42 ), '43foo' ),
            'recursive' => true,
        ) );

        $this->assertTrue( $validator->getRecursive() );

        $this->assertFalse( $validator->isValid( 'foo' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 'bar' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 'baz' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '0' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 0 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 42 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 43 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '43' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '42foo' ), implode( PHP_EOL, $validator->getMessages() ) );
    }

    /**
     * Test recursive & strict
     */
    public function testRecursiveStrict()
    {
        $validator = new Forbidden( array(
            'haystack'  => array( 'foo', 'baz' => array( 'bar', 42 ), '43foo' ),
            'strict'    => Forbidden::COMPARE_STRICT,
            'recursive' => true,
        ) );

        $this->assertTrue( $validator->getRecursive() );

        $this->assertFalse( $validator->isValid( 'foo' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 'bar' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 'baz' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '0' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 0 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 42 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '42' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 43 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '43' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '42foo' ), implode( PHP_EOL, $validator->getMessages() ) );
    }

}
