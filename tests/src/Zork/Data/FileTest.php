<?php

namespace Zork\Data;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * FileTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FileTest extends TestCase
{

    /**
     * Test file mime-type & data
     */
    public function testFile()
    {
        $file = __DIR__ . '/_files/file.txt';
        $data = new File( $file );

        $this->assertRegExp( '#^text/plain(\s*;.*)?$#', $data->getMimeType() );

        $this->assertSame(
            file( $file ),
            iterator_to_array( $data )
        );

        // test multiple use

        $this->assertSame(
            file( $file ),
            iterator_to_array( $data )
        );
    }

    /**
     * Test file not exists
     *
     * @expectedException   InvalidArgumentException
     */
    public function testFileNotExists()
    {
        new File( __DIR__ . '/_files/file-not-exists.txt' );
    }

}
