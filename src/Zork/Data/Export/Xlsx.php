<?php

namespace Zork\Data\Export;

use PHPExcel as Excel;
use PHPExcel_Writer_Excel2007 as ExcelWriter;

/**
 * Xlsx
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Xlsx extends TabularAbstract
{

    /**
     * @const string
     */
    const DEFAULT_MIMETYPE  = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    /**
     * @var string
     */
    protected $creator;

    /**
     * @var int
     */
    private $row = 1;

    /**
     * @var bool
     */
    private $excel = null;

    /**
     * @var bool
     */
    private $finished = false;

    /**
     * @return string
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param   string  $value
     * @return  \Zork\Data\File\Csv
     */
    public function setCreator( $value )
    {
        $this->creator = ( (string) $value ) ?: null;
        return $this;
    }

    /**
     * Encode a row
     *
     * @param   array|\Traversable  $row
     * @return  string
     */
    protected function encodeRow( $row )
    {
        $col    = 'A';
        $sheet  = $this->excel->getActiveSheet();

        foreach ( $row as $field )
        {
            $sheet->SetCellValue( ( $col++ ) . $this->row, $field );
        }

        return '';
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->row          = 1;
        $this->excel        = null;
        $this->finished     = false;

        $this->excel = new Excel;
        $this->excel->getProperties()->setCreator( $this->creator );
        $this->excel->getProperties()->setLastModifiedBy( $this->creator );
        $this->excel->setActiveSheetIndex( 0 );

        return parent::rewind();
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    public function valid()
    {
        return parent::valid() || ! $this->finished;
    }

    /**
     * Return the key of the current element
     *
     * @return scalar scalar on success, or <b>NULL</b> on failure.
     */
    public function key()
    {
        return $this->row;
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        if ( ! parent::valid() && ! $this->finished )
        {
            $writer = new ExcelWriter( $this->excel );
            $tmpfn  = tempnam( './public/tmp', 'xlsx' );
            $writer->save( $tmpfn );
            $cntnt  = file_get_contents( $tmpfn );
            @ unlink( $tmpfn );
            return $cntnt;
        }

        return parent::current();
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->row++;

        if ( ! parent::valid() && ! $this->finished )
        {
            $this->finished = true;
            return;
        }

        return parent::next();
    }

}
