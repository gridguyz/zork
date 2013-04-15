<?php

namespace Zork\Patcher;

/**
 * Result
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Result
{
    
    /**
     * @var string
     */
    const STATUS_NONE       = 'none';
    
    /**
     * @var string
     */
    const STATUS_SUCCESS    = 'success';
    
    /**
     * @var string
     */
    const STATUS_ERROR      = 'error';
    
    /**
     * @var string
     */
    const LOG_SQL           = 'sql';
    
    /**
     * @var string
     */
    const LOG_INFO          = 'info';
    
    /**
     * @var string
     */
    const LOG_ERROR         = 'error';
    
    /**
     * @var string
     */
    public $status;
    
    /**
     * @var array
     */
    public $schemaList = array();
    
    /**
     * @var array
     */
    public $patchList = array();
    
    /**
     * @var string
     */
    public $patch;
    
    /**
     * @var string
     */
    public $schema;
    
    /**
     * @var string
     */
    public $error;
    
    /**
     * @var array
     */
    public $logs = array();
    
    /**
     * Log
     * 
     * @param mixed $message
     * @param string $type
     * @return \Zork\Patcher\Result
     */
    public function log( $message, $type = self::LOG_INFO )
    {
        $this->logs[] = array( $type, $message );
        return $this;
    }
    
}
