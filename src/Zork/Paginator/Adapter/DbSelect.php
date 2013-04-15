<?php

namespace Zork\Paginator\Adapter;

use Zend\Db\Sql\Select;
use Zend\Paginator\Exception;
use Zend\Paginator\Adapter\DbSelect as ZendDbSelect;

/**
 * DbSelect
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DbSelect extends ZendDbSelect
{

    /**
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * @return \Zend\Db\Sql\Select
     */
    public function setSelect( Select $select )
    {
        if ( null !== $this->rowCount )
        {
            throw new Exception\RuntimeException(
                'Cannot change select, after rowCount calculated'
            );
        }

        $this->select = $select;
        return $this;
    }

    /**
     * @return \Zend\Db\Sql\Sql
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * Returns the total number of rows in the result set.
     *
     * @return integer
     */
    public function count()
    {
        if ( null !== $this->rowCount )
        {
            return $this->rowCount;
        }

        $originalSelect = $this->select;
        $this->select   = clone $this->select;

        $joins = $this->select
                      ->getRawState( Select::JOINS );

        $this->select
             ->quantifier( '' )
             ->reset( Select::JOINS );

        foreach ( $joins as $join )
        {
            $this->select
                 ->join( $join['name'],
                         $join['on'],
                         array(),
                         $join['type'] );
        }

        $result         = parent::count();
        $this->select   = $originalSelect;

        return $result;
    }

}
