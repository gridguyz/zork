<?php

namespace Zork\Data;

use ArrayObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * FileDataTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Data\FileData
 */
class FileDataTest extends TestCase
{

    /**
     * @var array
     */
    protected $data = array(
        "line 1\n",
        "line 2\n",
        "last line is empty\n",
    );

    /**
     * Test defaults
     */
    public function testDefaults()
    {
        $data = new FileData( $this->data );

        $this->assertSame( FileData::DEFAULT_MIMETYPE, $data->getMimeType() );
        $this->assertSame( $this->data, iterator_to_array( $data->getInnerIterator() ) );
        $this->assertSame( $this->data, iterator_to_array( $data ) );
    }

    /**
     * Test defaults
     */
    public function testIteratorAggregate()
    {
        $data = new FileData( new ArrayObject( $this->data ), array(
            'mimeType' => 'text/plain',
        ) );

        $this->assertSame( 'text/plain', $data->getMimeType() );
        $this->assertSame( $this->data, iterator_to_array( $data ) );
    }

    /**
     * Test not traversable
     *
     * @expectedException   InvalidArgumentException
     */
    public function testNotTraversable()
    {
        new FileData( null );
    }

}
