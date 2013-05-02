<?php

namespace Zork\Validator;

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * LessThanTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Validator\LessThan
 */
class LessThanTest extends TestCase
{

    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $this->assertEquals( 'token', ( new LessThan( 'token' ) )->getToken() );
        $this->assertEquals( 'token', ( new LessThan( array( 'token' => 'token' ) ) )->getToken() );
        $this->assertEquals( 'token', ( new LessThan( new ArrayIterator( array( 'token' => 'token' ) ) ) )->getToken() );

        $this->assertFalse( ( new LessThan() )->getEqual() );
        $this->assertFalse( ( new LessThan( 'token' ) )->getEqual() );
        $this->assertFalse( ( new LessThan( array( 'token' => 'token' ) ) )->getEqual() );
        $this->assertTrue( ( new LessThan( array( 'token' => 'token', 'equal' => true ) ) )->getEqual() );
        $this->assertFalse( ( new LessThan( new ArrayIterator( array( 'token' => 'token' ) ) ) )->getEqual() );
        $this->assertTrue( ( new LessThan( new ArrayIterator( array( 'token' => 'token', 'equal' => true ) ) ) )->getEqual() );
    }

    /**
     * Test validate
     */
    public function testValidate()
    {
        $validator = new LessThan( 'field' );

        $this->assertFalse( $validator->isValid( 1 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 1, array( 'field' => 0 ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 1, array( 'field' => 1 ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 1, array( 'field' => 2 ) ), implode( PHP_EOL, $validator->getMessages() ) );
    }

    /**
     * Test validate with equality
     */
    public function testValidateEqual()
    {
        $validator = new LessThan( array( 'token' => 'field', 'equal' => true ) );

        $this->assertFalse( $validator->isValid( 1 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 1, array( 'field' => 0 ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 1, array( 'field' => 1 ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 1, array( 'field' => 2 ) ), implode( PHP_EOL, $validator->getMessages() ) );
    }

}
