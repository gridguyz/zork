<?php

namespace Zork\Stdlib;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * StringTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Stdlib\Password
 */
class PasswordTest extends TestCase
{

    /**
     * @var string
     */
    public $examplePassword = '9x6mp19p6ssw0rd';

    /**
     * Test default salt generation
     */
    public function testDefaultSalt()
    {
        $this->assertRegExp(
            '/[\\.\\/0-9A-Za-z]{22}/',
            Password::salt()
        );
    }

    /**
     * Test default hash generation
     */
    public function testDefaultHash()
    {
        $opts = array(
            'cost' => 7,
            'salt' => Password::salt(),
        );

        $hash = Password::hash( $this->examplePassword, null, $opts );

        $this->assertNotEmpty( $hash );
        $this->assertNotEquals( $this->examplePassword, $hash );
        $this->assertTrue( Password::verify( $this->examplePassword, $hash ) );
        $this->assertFalse( Password::needsRehash( $hash, null, $opts ) );

        $info = Password::getInfo( $hash );
        $this->assertEquals( Password::ALGO_BCRYPT, $info['algo'] );
    }

    /**
     * Test unknown salt generation
     *
     * @expectedException   InvalidArgumentException
     */
    public function testUnknownSalt()
    {
        Password::salt( -1 );
    }

    /**
     * Test unknown hash generation
     */
    public function testUnknownHash()
    {
        $this->setExpectedException(
            function_exists( 'password_hash' )
                ? 'PHPUnit_Framework_Error'
                : 'InvalidArgumentException'
        );

        Password::hash( $this->examplePassword, -1 );
    }

}
