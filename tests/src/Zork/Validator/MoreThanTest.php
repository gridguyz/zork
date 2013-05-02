<?php

namespace Zork\Validator;

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * MoreThanTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Validator\MoreThan
 */
class MoreThanTest extends TestCase
{

    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $this->assertEquals( 'token', ( new MoreThan( 'token' ) )->getToken() );
        $this->assertEquals( 'token', ( new MoreThan( array( 'token' => 'token' ) ) )->getToken() );
        $this->assertEquals( 'token', ( new MoreThan( new ArrayIterator( array( 'token' => 'token' ) ) ) )->getToken() );

        $this->assertFalse( ( new MoreThan() )->getEqual() );
        $this->assertFalse( ( new MoreThan( 'token' ) )->getEqual() );
        $this->assertFalse( ( new MoreThan( array( 'token' => 'token' ) ) )->getEqual() );
        $this->assertTrue( ( new MoreThan( array( 'token' => 'token', 'equal' => true ) ) )->getEqual() );
        $this->assertFalse( ( new MoreThan( new ArrayIterator( array( 'token' => 'token' ) ) ) )->getEqual() );
        $this->assertTrue( ( new MoreThan( new ArrayIterator( array( 'token' => 'token', 'equal' => true ) ) ) )->getEqual() );
    }

    /**
     * Test validate
     */
    public function testValidate()
    {
        $validator = new MoreThan( 'field' );

        $this->assertFalse( $validator->isValid( 1 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 1, array( 'field' => 0 ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 1, array( 'field' => 1 ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 1, array( 'field' => 2 ) ), implode( PHP_EOL, $validator->getMessages() ) );
    }

    /**
     * Test validate with equality
     */
    public function testValidateEqual()
    {
        $validator = new MoreThan( array( 'token' => 'field', 'equal' => true ) );

        $this->assertFalse( $validator->isValid( 1 ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 1, array( 'field' => 0 ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 1, array( 'field' => 1 ) ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( 1, array( 'field' => 2 ) ), implode( PHP_EOL, $validator->getMessages() ) );
    }

}
