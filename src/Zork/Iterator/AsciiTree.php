<?php

namespace Zork\Iterator;

use RecursiveIterator;
use RecursiveIteratorIterator;

/**
 * AsciiTree
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AsciiTree extends RecursiveIteratorIterator
{
    
    /**
     * For keys
     * 
     * @var string 
     */
    public $padding     = '│';
    
    /**
     * For keys
     * 
     * @var string 
     */
    public $lastChild   = '└';
    
    /**
     * For keys
     * 
     * @var string 
     */
    public $child       = '├';
    
    /**
     * For keys
     * 
     * @var string 
     */
    public $hasChildren = '┬';
    
    /**
     * For keys
     * 
     * @var string 
     */
    public $noChildren  = '─';
    
    /**
     * Previous is valid
     * 
     * @var bool
     */
    protected $prevValid;
    
    /**
     * Previous value
     * 
     * @var bool
     */
    protected $prevValue;
    
    /**
     * Previous key
     * 
     * @var bool
     */
    protected $prevKey;
    
    /**
     * Constructor
     * 
     * @param RecursiveIterator $iterator
     */
    public function __construct( RecursiveIterator $iterator )
    {
        parent::__construct( $iterator, parent::SELF_FIRST );
    }
    
    /**
     * Rewind the iterator to the first element of the top level inner iterator
     * 
     * @return void No value is returned.
     */
    public function rewind()
    {
        parent::rewind();
        $this->next();
    }
    
    /**
     * Move forward to the next element
     * 
     * @return void No value is returned.
     */
    public function next()
    {
        $this->prevValid = parent::valid();
        
        if ( $this->prevValid )
        {
            $sub = $this->getSubIterator();
            
            $lastDepth   = $this->getDepth();
            $hasChildren = $sub->hasChildren();
            
            $this->prevValue = parent::current();
            $this->prevKey   = str_repeat( $this->padding, $this->getDepth() );
            
            parent::next();
            
            $lastChild = $lastDepth > $this->getDepth();
            $this->prevKey  .= $lastChild ? $this->lastChild : $this->child;
            $this->prevKey  .= $hasChildren ? $this->hasChildren : $this->noChildren;
        }
        else
        {
            $this->prevValue = null;
            $this->prevKey   = null;
        }
    }
    
    /**
     * Checks if current position is valid
     * 
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns <b>true</b> on success or <b>false</b> on failure.
     */
    public function valid()
    {
        return $this->prevValid;
    }
    
    /**
     * Access the current element value
     * 
     * @return mixed The current elements value.
     */
    public function current()
    {
        return $this->prevValue;
    }
    
    /**
     * Return the key of the current element
     * 
     * @return scalar scalar on success, or <b>null</b> on failure.
     */
    public function key()
    {
        return $this->prevKey;
    }
    
}
