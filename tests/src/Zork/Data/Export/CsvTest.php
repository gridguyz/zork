<?php

namespace Zork\Data\Export;

use ArrayIterator;
use Zork\Data\Table;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * CsvTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CsvTest extends TestCase
{

    /**
     * @var array
     */
    public $data = array(
        array( 'ID' => 1, 'prop1' => null,  'prop2' => 0 ),
        array( 'ID' => 2, 'prop1' => true,  'prop2' => array( '0', '0' ) ),
        array( 'ID' => 3, 'prop1' => false, 'prop2' => '0"0' ),
    );

    /**
     * @var array
     */
    public $fields = array(
        'ID'    => Table::INTEGER,
        'prop1' => Table::BOOLEAN,
        'prop2' => Table::IDENTICAL,
    );

    /**
     * @var Table
     */
    protected $table;

    /**
     * This method is called before the first test of this test class is run
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        include_once __DIR__ . '/_files/CsvTestClasses.php';
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->table = new Table(
            new CsvTest\ArrayIteratorAware( $this->data ),
            $this->fields
        );
    }

    /**
     * Test defaults
     */
    public function testDefaults()
    {
        $csv = new Csv( $this->table );

        $this->assertTrue( $csv->getSendHeaders() );
        $this->assertEmpty( $csv->getFieldNames() );
        $this->assertSame(
            array(
                'ID'    => 'ID',
                'prop1' => 'prop1',
                'prop2' => 'prop2',
            ),
            $csv->getAllFieldNames()
        );

        $this->assertSame( Csv::DEFAULT_EOL, $csv->getEol() );
        $this->assertSame( Csv::DEFAULT_SEPARATOR, $csv->getSeparator() );

        $this->assertSame(
            array(
                implode( Csv::DEFAULT_SEPARATOR, array( '"ID"', 'prop1', 'prop2' ) ) . Csv::DEFAULT_EOL,
                implode( Csv::DEFAULT_SEPARATOR, array( '1', '', '0' ) ) . Csv::DEFAULT_EOL,
                implode( Csv::DEFAULT_SEPARATOR, array( '2', '1', '"0' . Csv::DEFAULT_EOL . '0"' ) ) . Csv::DEFAULT_EOL,
                implode( Csv::DEFAULT_SEPARATOR, array( '3', '', '"0""0"' ) ) . Csv::DEFAULT_EOL,
            ),
            array_values( iterator_to_array( $csv ) )
        );
    }

    /**
     * Test options
     */
    public function testOptions()
    {
        $csv = new Csv( $this->table, array(
            'sendHeaders'   => false,
            'fieldNames'    => new ArrayIterator( array( 'ID' => 'id' ) ),
            'eol'           => 'windows',
            'separator'     => 'semicolon',
        ) );

        $this->assertFalse( $csv->getSendHeaders() );
        $this->assertSame(
            array(
                'ID'    => 'id',
            ),
            $csv->getFieldNames()
        );

        $this->assertSame(
            array(
                'ID'    => 'id',
                'prop1' => 'prop1',
                'prop2' => 'prop2',
            ),
            $csv->getAllFieldNames()
        );

        $this->assertSame( $eol = Csv::$eolAliases['windows'], $csv->getEol() );
        $this->assertSame( $sep = Csv::$separatorAliases['semicolon'], $csv->getSeparator() );

        $this->assertSame(
            array(
                implode( $sep, array( '1', '', '0' ) ) . $eol,
                implode( $sep, array( '2', '1', '"0' . $eol . '0"' ) ) . $eol,
                implode( $sep, array( '3', '', '"0""0"' ) ) . $eol,
            ),
            array_values( iterator_to_array( $csv ) )
        );
    }

}
