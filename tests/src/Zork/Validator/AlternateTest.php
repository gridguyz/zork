<?php

namespace Zork\Validator;

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * AlternateTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Validator\Alternate
 */
class AlternateTest extends TestCase
{

    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $this->assertEquals( 'token', ( new Alternate( 'token' ) )->getToken() );
        $this->assertEquals( 'token', ( new Alternate( array( 'token' => 'token' ) ) )->getToken() );
        $this->assertEquals( 'token', ( new Alternate( new ArrayIterator( array( 'token' => 'token' ) ) ) )->getToken() );
    }

    /**
     * Test validate
     */
    public function testValidate()
    {
        $validator = new Alternate( 'field' );

        $this->assertFalse( $validator->isValid( '' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( 'not-empty' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertTrue( $validator->isValid( '0' ), implode( PHP_EOL, $validator->getMessages() ) );
        $this->assertFalse( $validator->isValid( '', array( 'field' => '' ), implode( PHP_EOL, $validator->getMessages() ) ) );
        $this->assertTrue( $validator->isValid( '', array( 'field' => 'not-empty' ), implode( PHP_EOL, $validator->getMessages() ) ) );
        $this->assertTrue( $validator->isValid( '', array( 'field' => '0' ), implode( PHP_EOL, $validator->getMessages() ) ) );
    }

}
