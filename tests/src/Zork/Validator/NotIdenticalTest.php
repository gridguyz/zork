<?php

namespace Zork\Validator;

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * NotIdenticalTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Validator\NotIdentical
 */
class NotIdenticalTest extends TestCase
{

    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $this->assertEquals( 'token', ( new NotIdentical( 'token' ) )->getToken() );
        $this->assertEquals( 'token', ( new NotIdentical( array( 'token' => 'token' ) ) )->getToken() );
        $this->assertEquals( 'token', ( new NotIdentical( new ArrayIterator( array( 'token' => 'token' ) ) ) )->getToken() );

        $this->assertTrue( ( new NotIdentical() )->getStrict() );
        $this->assertTrue( ( new NotIdentical( 'token' ) )->getStrict() );
        $this->assertTrue( ( new NotIdentical( array( 'token' => 'token' ) ) )->getStrict() );

        $this->assertFalse( ( new NotIdentical() )->getLiteral() );
        $this->assertFalse( ( new NotIdentical( 'token' ) )->getLiteral() );
        $this->assertFalse( ( new NotIdentical( array( 'token' => 'token' ) ) )->getLiteral() );

        $this->assertFalse( ( new NotIdentical( array( 'token' => 'token', 'strict' => false ) ) )->getStrict() );
        $this->assertTrue( ( new NotIdentical( new ArrayIterator( array( 'token' => 'token', 'literal' => true ) ) ) )->getLiteral() );
    }

    /**
     * Test validate
     */
    public function testValidate()
    {
        $validator = new NotIdentical( 'field' );

        $this->assertTrue( $validator->isValid( 1 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 1, array( 'field' => 0 ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 1, array( 'field' => 1 ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 1, array( 'field' => 2 ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 1, array( 'field' => '1' ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 1, array( 'field' => null ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 1, array() ), implode( PHP_EOL, $validator->getMessages() ) );

        $validator = new NotIdentical();
        $this->assertFalse( $validator->isValid( 1 ), implode( PHP_EOL, $validator->getMessages() ) );

        $validator = new NotIdentical( array( 'token' => array( 't1' => array( 't2' => 't3' ) ) ) );
        $this->assertTrue( $validator->isValid( 1 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 1, array( 't1' => array( 't2' => array( 't3' => 1 ) ) ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 1, array( 't1' => array( 't2' => array( 1 ) ) ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 1, array( 't1' => array( 't2' => array( 't3' => array( 't4' => 1 ) ) ) ) ), implode( PHP_EOL, $validator->getMessages() ) );
    }

    /**
     * Test validate with non-strict mode
     */
    public function testValidateNotStrict()
    {
        $validator = new NotIdentical( array( 'token' => 'field', 'strict' => false ) );

        $this->assertTrue( $validator->isValid( 1 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 1, array( 'field' => 0 ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 1, array( 'field' => 1 ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 1, array( 'field' => 2 ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 1, array( 'field' => '1' ) ), implode( PHP_EOL, $validator->getMessages() ) );
    }

    /**
     * Test validate with literal
     */
    public function testValidateLiteral()
    {
        $validator = new NotIdentical( array( 'token' => 1, 'literal' => true ) );

        $this->assertTrue( $validator->isValid( 0 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 1 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 2 ), implode( PHP_EOL, $validator->getMessages() ) );
    }

}
