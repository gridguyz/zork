<?php

namespace Zork\Model\Mapper\DbAware;

use Zork\Model\Structure\MapperAwareAbstract;

class TestReadOnlyStructure extends MapperAwareAbstract
{

    public $int;
    public $bool;
    public $float;
    public $string;
    public $text;
    public $date;
    public $datetime;
    public $numbers = array();

}

class TestReadOnlyMapper extends ReadOnlyMapperAbstract
{

    /**
     * Table name used in all queries
     *
     * @var string
     */
    protected static $tableName = 'table_name';

    /**
     * Primary key(s) used in find()
     *
     * @var string|array
     */
    protected static $primaryKeys = 'int';

    /**
     * Default column-conversion functions as types;
     * used in selected(), deselect()
     *
     * @var array
     */
    protected static $columns = array(
        'int'           => self::INT_READONLY,
        'bool'          => self::BOOL_READONLY,
        'float'         => self::FLOAT_READONLY,
        'string'        => self::STRING_READONLY,
        'text'          => self::TEXT_READONLY,
        'date'          => self::DATE_READONLY,
        'datetime'      => self::DATETIME_READONLY,
        'numbers'       => self::NUMBERARRAY_READONLY,
    );

    /**
     * Constructor
     *
     * @param TestReadOnlyStructure $structurePrototype
     */
    public function __construct( TestReadOnlyStructure $structurePrototype = null )
    {
        parent::__construct( $structurePrototype ?: new TestReadOnlyStructure );
    }

}

class TestReadWriteStructure extends MapperAwareAbstract
{

    public $int;
    public $bool;
    public $float;
    public $string;
    public $text;
    public $date;
    public $datetime;
    public $numbers = array();
    public $others = array(
        'other1' => 'default1',
        'other2' => 'default2',
    );

}

class TestReadWriteMapper extends ReadWriteMapperAbstract
{

    /**
     * Table name used in all queries
     *
     * @var string
     */
    protected static $tableName = 'table_name';

    /**
     * Primary key(s) used in find()
     *
     * @var string|array
     */
    protected static $primaryKeys = 'int';

    /**
     * Use sql's "DEFAULT" keyword on
     * save if value is NULL
     *
     * @var array
     */
    protected static $useDefaultOnSave = array(
        'string' => true,
    );

    /**
     * Define the sequences for keys that other than Postgresql
     * standard generated sequence names ("{$table}_{$column}_seq")
     *
     * @var array
     */
    protected static $sequences = array(
        'int' => 'int_seq',
    );

    /**
     * Default column-conversion functions as types;
     * used in selected(), deselect()
     *
     * @var array
     */
    protected static $columns = array(
        'int'           => self::INT,
        'bool'          => self::BOOL,
        'float'         => self::FLOAT,
        'string'        => self::STRING,
        'text'          => self::TEXT,
        'date'          => self::DATE,
        'datetime'      => self::DATETIME,
        'numbers'       => self::NUMBERARRAY,
    );

    /**
     * Constructor
     *
     * @param TestReadWriteStructure $structurePrototype
     */
    public function __construct( TestReadWriteStructure $structurePrototype = null )
    {
        parent::__construct( $structurePrototype ?: new TestReadWriteStructure );
    }

}
