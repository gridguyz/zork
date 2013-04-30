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
        $hash = Password::hash( $this->examplePassword );

        $this->assertNotEquals( $this->examplePassword, $hash );
        $this->assertTrue( Password::verify( $this->examplePassword, $hash ) );
        $this->assertFalse( Password::needsRehash( $hash ) );

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
     *
     * @expectedException   InvalidArgumentException
     */
    public function testUnknownHash()
    {
        Password::hash( $this->examplePassword, -1 );
    }

}
