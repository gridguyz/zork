<?php

namespace Zork\Stdlib;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * MessageTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Stdlib\Message
 */
class MessageTest extends TestCase
{

    /**
     * Test default values
     */
    public function testDefaults()
    {
        $message = new Message( 'message' );
        $this->assertEquals( 'message', $message->getMessage() );
        $this->assertTrue( $message->hasTranslations() );
        $this->assertEquals( Message::DEFAULT_TEXT_DOMAIN, $message->getTextDomain() );
        $this->assertEquals( Message::DEFAULT_LEVEL, $message->getLevel() );
    }

    /**
     * Test to disable translations
     */
    public function testNoTextDomain()
    {
        $message = new Message( 'message', false );
        $this->assertEquals( 'message', $message->getMessage() );
        $this->assertFalse( $message->hasTranslations() );
        $this->assertFalse( $message->getTextDomain() );
    }

    /**
     * Full use test
     */
    public function testFullUse()
    {
        $message = new Message( 'message', 'textDomain', Message::LEVEL_ERROR );
        $this->assertEquals( 'message', $message->getMessage() );
        $this->assertTrue( $message->hasTranslations() );
        $this->assertEquals( 'textDomain', $message->getTextDomain() );
        $this->assertEquals( Message::LEVEL_ERROR, $message->getLevel() );
    }

}
