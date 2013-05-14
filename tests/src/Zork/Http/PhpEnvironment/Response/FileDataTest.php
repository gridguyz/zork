<?php

namespace Zork\Http\PhpEnvironment\Response;

use Zork\Data;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * FileDataTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Http\PhpEnvironment\FileData
 */
class FileDataTest extends TestCase
{

    /**
     * @var string
     */
    protected $file;

    /**
     * @var Data\File
     */
    protected $fileData;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->file     = __DIR__ . '/_files/output.txt';
        $this->fileData = new Data\File( $this->file );
    }

    /**
     * Test output
     */
    public function testOutput()
    {
        $content    = file_get_contents( $this->file );
        $filedata   = FileData::fromData( $this->fileData );

        $this->assertEquals( $content, $filedata->getContent() );
        $this->assertFalse( $filedata->contentSent() );
        $this->expectOutputString( $content );

        $filedata->sendContent()
                 ->sendContent();

        $this->assertTrue( $filedata->contentSent() );
    }

    /**
     * Test attachment
     */
    public function testAttachment()
    {
        $filedata = FileData::fromData( $this->fileData, 'attachment.txt' );

        $this->assertEquals(
            'text/plain',
            $filedata->getHeaders()
                     ->get( 'Content-Type' )
                     ->getFieldValue()
        );

        $this->assertRegExp(
            '/^\s*attachment\s*;\s*filename\s*=\s*"attachment.txt"/',
            $filedata->getHeaders()
                     ->get( 'Content-Disposition' )
                     ->getFieldValue()
        );
    }

    /**
     * Test empty file
     *
     * @expectedException   RuntimeException
     */
    public function testSetContent()
    {
        $readfile = FileData::fromData( $this->fileData );
        $readfile->setContent( 'foo-bar' );
    }

}
