<?php

namespace Zork\Data;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * TableTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TableTest extends TestCase
{

    /**
     * @var array
     */
    public $data = array(
        array( 'int' => 1,   'bool' => null,  'string' => 0 ),
        array( 'int' => 2,   'bool' => true,  'string' => 0.1 ),
        array( 'int' => '3', 'bool' => false, 'string' => '0"0' ),
    );

    /**
     * @var array
     */
    public $fields = array(
        'int'       => Table::INTEGER,
        'bool'      => Table::BOOLEAN,
        'string'    => Table::STRING,
    );

    /**
     * This method is called before the first test of this test class is run
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        include_once __DIR__ . '/_files/TableTestClasses.php';
    }

    /**
     * Test defaults
     */
    public function testDefaults()
    {
        $table = new Table( $this->data, $this->fields );

        $this->assertSame( array_keys( $this->fields ), $table->getFieldNames() );
        $this->assertInstanceOf( 'ArrayIterator', $table->getInnerIterator() );

        $this->assertSame(
            array(
                array( 'int' => 1, 'bool' => null,  'string' => '0' ),
                array( 'int' => 2, 'bool' => true,  'string' => '0.1' ),
                array( 'int' => 3, 'bool' => false, 'string' => '0"0' ),
            ),
            iterator_to_array( $table )
        );
    }

    /**
     * Test ArrayAccess extraction
     */
    public function testArrayAccess()
    {
        $table = new Table(
            new TableTest\ArrayAccessAware( $this->data ),
            $this->fields
        );

        $this->assertSame(
            array(
                array( 'int' => 1, 'bool' => null,  'string' => '0' ),
                array( 'int' => 2, 'bool' => true,  'string' => '0.1' ),
                array( 'int' => 3, 'bool' => false, 'string' => '0"0' ),
            ),
            iterator_to_array( $table )
        );
    }

    /**
     * Test stdClass extraction
     */
    public function testStdClass()
    {
        $table = new Table(
            new TableTest\StdClassAware( $this->data ),
            $this->fields
        );

        $this->assertSame(
            array(
                array( 'int' => 1, 'bool' => null,  'string' => '0' ),
                array( 'int' => 2, 'bool' => true,  'string' => '0.1' ),
                array( 'int' => 3, 'bool' => false, 'string' => '0"0' ),
            ),
            iterator_to_array( $table )
        );
    }

    /**
     * Test getOption() extraction
     */
    public function testGetOption()
    {
        $table = new Table(
            new TableTest\GetOptionAware( $this->data ),
            $this->fields
        );

        $this->assertSame(
            array(
                array( 'int' => 1, 'bool' => null,  'string' => '0' ),
                array( 'int' => 2, 'bool' => true,  'string' => '0.1' ),
                array( 'int' => 3, 'bool' => false, 'string' => '0"0' ),
            ),
            iterator_to_array( $table )
        );
    }

    /**
     * Test not iterator
     *
     * @expectedException   InvalidArgumentException
     */
    public function testNotIterator()
    {
        new Table( null, null );
    }

    /**
     * Test export
     */
    public function testExport()
    {
        $table = new Table( $this->data, $this->fields );
        $this->assertInstanceOf( 'Zork\Data\Export\Csv', $table->export( 'csv' ) );
        $this->assertInstanceOf( 'Zork\Data\Export\Csv', $table->export( 'Zork\Data\Export\Csv' ) );
    }

    /**
     * Test unknown export type
     *
     * @expectedException   InvalidArgumentException
     */
    public function testUnknownExport()
    {
        $table = new Table( $this->data, $this->fields );
        $table->export( 'unknown_type' );
    }

}
