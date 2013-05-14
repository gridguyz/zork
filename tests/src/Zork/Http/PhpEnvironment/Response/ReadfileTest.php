<?php

namespace Zork\Http\PhpEnvironment\Response;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * ReadfileTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ReadfileTest extends TestCase
{

    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $tmp;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->tmp  = tempnam( __DIR__ . '/_files/', 'output' );
        $this->file = __DIR__ . '/_files/output.txt';
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        if ( file_exists( $this->tmp ) )
        {
            unlink( $this->tmp );
        }
    }

    /**
     * Test output
     */
    public function testOutput()
    {
        $content    = file_get_contents( $this->file );
        $readfile   = Readfile::fromFile( $this->file );

        $this->assertEquals( $content, $readfile->getContent() );
        $this->assertFalse( $readfile->contentSent() );
        $this->expectOutputString( $content );

        $readfile->sendContent();
        $this->assertTrue( $readfile->contentSent() );
    }

    /**
     * Test attachment
     */
    public function testAttachment()
    {
        $rf1 = Readfile::fromFile( $this->file, 'text/plain', true );
        $rf2 = Readfile::fromFile( $this->file, 'text/plain', 'attachment.txt' );

        $this->assertEquals(
            'text/plain',
            $rf1->getHeaders()
                ->get( 'Content-Type' )
                ->getFieldValue()
        );

        $this->assertRegExp(
            '/^\s*attachment\s*;\s*filename\s*=\s*"output.txt"/',
            $rf1->getHeaders()
                ->get( 'Content-Disposition' )
                ->getFieldValue()
        );

        $this->assertRegExp(
            '/^\s*attachment\s*;\s*filename\s*=\s*"attachment.txt"/',
            $rf2->getHeaders()
                ->get( 'Content-Disposition' )
                ->getFieldValue()
        );
    }

    /**
     * Test unlink
     */
    public function testUnlink()
    {
        copy( $this->file, $this->tmp );
        clearstatcache( true, $this->tmp );

        $this->assertFileExists( $this->tmp );
        $content  = file_get_contents( $this->tmp );
        $readfile = Readfile::fromFile( $this->tmp, 'text/plain', false, true );
        $this->assertTrue( $readfile->getUnlink() );

        $this->expectOutputString( $content );

        $readfile->sendContent()
                 ->sendContent();
    }

    /**
     * Test not file
     *
     * @expectedException   InvalidArgumentException
     */
    public function testNotFile()
    {
        Readfile::fromFile( __DIR__ . '/_files/not-a-file.txt' );
    }

    /**
     * Test empty file
     *
     * @expectedException   InvalidArgumentException
     */
    public function testEmptyFile()
    {
        Readfile::fromFile( __DIR__ . '/_files/empty.txt' );
    }

    /**
     * Test empty file
     *
     * @expectedException   RuntimeException
     */
    public function testSetContent()
    {
        $readfile = Readfile::fromFile( $this->file );
        $readfile->setContent( 'foo-bar' );
    }

}
