<?php

namespace Zork\Model\Mapper\DbAware;

use Traversable;
use Zork\Stdlib\OptionsTrait;
use Zork\Stdlib\DateTime;
use Zend\Paginator\Paginator;
use Zork\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression as SqlExpression;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zork\Model\Exception;
use Zork\Model\DbAdapterAwareTrait;
use Zork\Model\MapperAwareInterface;
use Zork\Model\DbAdapterAwareInterface;
use Zork\Model\Mapper\ReadOnlyMapperInterface;
use Zork\Model\Mapper\ReadListMapperInterface;
use Zork\Model\Structure\StructureAbstract;
use Zork\Paginator\Adapter\DbSelect as DbSelectPaginator;

/**
 * ReadOnlyMapperAbstract
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class ReadOnlyMapperAbstract
    implements HydratorInterface,
               DbAdapterAwareInterface,
               DbSchemaAwareInterface,
               ReadOnlyMapperInterface,
               ReadListMapperInterface
{

    use OptionsTrait,
        DbAdapterAwareTrait,
        DbSchemaAwareTrait;

    /**
     * @var string
     */
    const DEFAULT_TABLE         = '*';

    /**
     * Integer type-converters for selected(), deselect()
     * @var array
     */
    const INT_READONLY          = 'intval;';

    /**
     * Integer type-converters for selected(), deselect()
     * @var array
     */
    const INTEGER_READONLY      = self::INT_READONLY;

    /**
     * Boolean type-converters for selected(), deselect()
     * @var array
     */
    const BOOL_READONLY         = 'self::toBool;';

    /**
     * Boolean type-converters for selected(), deselect()
     * @var array
     */
    const BOOLEAN_READONLY      = self::BOOL_READONLY;

    /**
     * Float type-converters for selected(), deselect()
     * @var array
     */
    const FLOAT_READONLY        = 'floatval;';

    /**
     * Double (float) type-converters for selected(), deselect()
     * @var array
     */
    const DOUBLE_READONLY       = self::FLOAT_READONLY;

    /**
     * Real (float) type-converters for selected(), deselect()
     * @var array
     */
    const REAL_READONLY         = self::FLOAT_READONLY;

    /**
     * String type-converters for selected(), deselect()
     * @var array
     */
    const STR_READONLY          = 'strval;';

    /**
     * String type-converters for selected(), deselect()
     * @var array
     */
    const STRING_READONLY       = self::STR_READONLY;

    /**
     * Text (string) type-converters for selected(), deselect()
     * @var array
     */
    const TEXT_READONLY         = self::STR_READONLY;

    /**
     * Char (string) type-converters for selected(), deselect()
     * @var array
     */
    const CHAR_READONLY         = self::STR_READONLY;

    /**
     * Varchar (string) type-converters for selected(), deselect()
     * @var array
     */
    const VARCHAR_READONLY      = self::STR_READONLY;

    /**
     * Date type-converters for selected(), deselect()
     * @var array
     */
    const DATE_READONLY         = 'self::toDate;';

    /**
     * Date-time type-converters for selected(), deselect()
     * (use time-zones)
     * @var array
     */
    const DATETIME_READONLY     = self::DATE_READONLY;

    /**
     * Number-array type-converters for selected(), deselect()
     * @var array
     */
    const NUMARRAY_READONLY     = 'self::toNumberArray;';

    /**
     * Number-array type-converters for selected(), deselect()
     * @var array
     */
    const NUMBERARRAY_READONLY  = self::NUMARRAY_READONLY;

    /**
     * Integer-array type-converters for selected(), deselect()
     * @var array
     */
    const INTARRAY_READONLY     = self::NUMARRAY_READONLY;

    /**
     * Integer-array type-converters for selected(), deselect()
     * @var array
     */
    const INTEGERARRAY_READONLY = self::NUMARRAY_READONLY;

    /**
     * Float-array type-converters for selected(), deselect()
     * @var array
     */
    const FLOATARRAY_READONLY   = self::NUMARRAY_READONLY;

    /**
     * Double-(float)-array type-converters for selected(), deselect()
     * @var array
     */
    const DOUBLEARRAY_READONLY  = self::NUMARRAY_READONLY;

    /**
     * Real-(float)-array type-converters for selected(), deselect()
     * @var array
     */
    const REALARRAY_READONLY    = self::NUMARRAY_READONLY;

    /**
     * Table name used in all queries
     *
     * @example <pre>
     * protected static $tableName = 'my_table_name';
     * </pre>
     *
     * @abstract
     * @var string
     */
    protected static $tableName = '';

    /**
     * Get the table-name
     *
     * @return string
     */
    protected function getTableName()
    {
        if ( empty( static::$tableName ) )
        {
            throw new Exception\LogicException(
                '$tableName not implemented'
            );
        }

        return $this->getTableInSchema( static::$tableName );
    }

    /**
     * Primary key(s) used in find()
     *
     * @example <pre>
     * protected static $primaryKey = 'id';
     * // or:
     * protected static $primaryKey = array( 'symbol', 'type' );
     * </pre>
     *
     * @abstract
     * @var string|array
     */
    protected static $primaryKeys = 'id';

    /**
     * Get the primary-keys
     *
     * @return array
     */
    protected static function getPrimaryKeys()
    {
        return (array) static::$primaryKeys;
    }

    /**
     * Define the sequences for keys that other than Postgresql
     * standard generated sequence names ("{$table}_{$column}_seq")
     *
     * @example <pre>
     * protected static $sequences = array( 'id' => 'my_seq_name' );
     * </pre>
     *
     * @abstract
     * @var array
     */
    protected static $sequences = array();

    /**
     * Default column-conversion functions as types;
     * used in selected(), deselect()
     *
     * @example <pre>
     * protected static $columns = array(
     *     'id'        => self::INT,
     *     'symbol'    => self::STRING,
     *     'enabled'   => self::BOOL,
     *     'complex'   => array( 'Complex::transform', 'Complex::detransform' )
     *     // etc...
     * );
     * </pre>
     *
     * @abstract
     * @var array
     */
    protected static $columns = array();

    /**
     * Get columns for the table
     *
     * The default implementation works from the class properties:
     * <code>$_primaryKey, $_defaultValues, $_columnTypes</code>
     *
     * @return array
     */
    protected static function getColumns()
    {
        if ( empty( static::$columns ) )
        {
            throw new Exception\LogicException(
                '$columns not implemented'
            );
        }

        return static::$columns;
    }

    /**
     * Sql-object
     *
     * @var \Zend\Db\Sql\Sql
     */
    private $sql;

    /**
     * Get a Zend\Db\Sql\Sql object
     *
     * @param null|string|TableIdentifier $table default: self::DEFAULT_TABLE
     * @return \Zend\Db\Sql\Sql
     */
    protected function sql( $table = self::DEFAULT_TABLE )
    {
        if ( self::DEFAULT_TABLE === $table )
        {
            if ( null === $this->sql ||
                 $this->sql
                      ->getTable() != $this->getTableName() )
            {
                $this->sql = new Sql(
                    $this->getDbAdapter(),
                    $this->getTableName()
                );
            }

            return $this->sql;
        }

        return new Sql(
            $this->getDbAdapter(),
            $table
        );
    }

    /**
     * Structure prototype for the mapper
     *
     * @var \Zork\Model\Structure\StructureAbstract
     */
    protected $structurePrototype;

    /**
     * Get structure prototype
     *
     * @return \Zork\Model\Structure\StructureAbstract
     */
    public function getStructurePrototype()
    {
        return $this->structurePrototype;
    }

    /**
     * Set structure prototype
     *
     * @param \Zork\Model\Structure\StructureAbstract $structurePrototype
     * @return \Zork\Model\Mapper\DbAware\ReadOnlyMapperAbstract
     */
    public function setStructurePrototype( $structurePrototype )
    {
        if ( $structurePrototype instanceof MapperAwareInterface )
        {
            $structurePrototype->setMapper( $this );
        }

        $this->structurePrototype = $structurePrototype;
        return $this;
    }

    /**
     * Create structure from plain data
     *
     * @param array $data
     * @return \Zork\Model\Structure\StructureAbstract
     */
    protected function createStructure( array $data )
    {
        $structure = clone $this->structurePrototype;
        $structure->setOptions( $data );

        if ( $structure instanceof MapperAwareInterface )
        {
            $structure->setMapper( $this );
        }

        return $structure;
    }

    /**
     * Constructor
     *
     * @param \Zork\Model\Structure\StructureAbstract $structurePrototype
     */
    public function __construct( StructureAbstract $structurePrototype = null )
    {
        $this->setStructurePrototype( $structurePrototype );
    }

    /**
     * Get select() default columns
     *
     * @return array
     */
    protected function getSelectColumns( $columns = null )
    {
        if ( null === $columns )
        {
            $columns = array_keys( static::getColumns() );
        }
        else
        {
            $columns = (array) $columns;
        }

        return $columns;
    }

    /**
     * Get nature elements query from datasource
     *
     * @param array|null $columns
     * @param bool $prefixColumnsWithTable
     * @return \Zend\Db\Sql\Select
     */
    public function select( $columns = null, $prefixColumnsWithTable = true )
    {
        return $this->sql()
                    ->select()
                    ->columns(
                        $this->getSelectColumns( $columns ),
                        $prefixColumnsWithTable
                    );
    }

    /**
     * Transforms the selected data into the structure object
     *
     * @param array $data
     * @return \Zork\Model\Structure\StructureAbstract
     */
    public function selected( array $data )
    {
        foreach ( static::getColumns() as $key => $transformators )
        {
            if ( ! is_array( $transformators ) )
            {
                $transformators = explode( ';', $transformators );
            }

            if ( ! empty( $transformators[0] ) )
            {
                if ( null !== $data[$key] )
                {
                    $data[$key] = call_user_func(
                        $transformators[0],
                        $data[$key]
                    );
                }
            }
        }

        return $this->createStructure( $data );
    }

    /**
     * Get one element structure from datasource according to the conditions
     *
     * @param array|mixed $primaryKeys
     * @return \Zork\Model\Structure\StructureAbstract
     */
    public function find( $primaryKeys )
    {
        $where          = array();
        $table          = static::$tableName;
        $primaryKeys    = is_array( $primaryKeys )
                        ? $primaryKeys : func_get_args();

        foreach ( static::getPrimaryKeys() as $index => $primary )
        {
            $where[$table . '.' . $primary] = $primaryKeys[ $index ];
        }

        return $this->findOne( $where );
    }

    /**
     * Get one element structure from datasource according to the conditions
     *
     * @param array $where
     * @param array $orders
     * @return \Zork\Model\Structure\StructureAbstract
     */
    public function findOne( $where = null, $order = null )
    {
        $select = $this->select()
                       ->where( $where ?: array() )
                       ->order( $order ?: array() )
                       ->limit( 1 );

        /* @var $result \Zend\Db\Adapter\Driver\ResultInterface */

        $result = $this->sql()
                       ->prepareStatementForSqlObject( $select )
                       ->execute();

        $affected = $result->getAffectedRows();

        if ( $affected < 1 )
        {
            return null;
        }

        if ( $affected > 1 )
        {
            // Just-in-case
            // @codeCoverageIgnoreStart
            throw new \Zend\Db\Exception\UnexpectedValueException(
                'Too many rows'
            );
            // @codeCoverageIgnoreEnd
        }

        foreach ( $result as $row )
        {
            return $this->selected( $row );
        }
    }

    /**
     * Get all element structure from datasource according to the conditions
     *
     * @param mixed|null $where
     * @param mixed|null $order
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findAll( $where  = null,
                             $order  = null,
                             $limit  = null,
                             $offset = null )
    {
        $select = $this->select()
                       ->where( $where ?: array() )
                       ->order( $order ?: array() )
                       ->limit( $limit )
                       ->offset( $offset );

        /* @var $result \Zend\Db\Adapter\Driver\ResultInterface */

        $return = array();
        $result = $this->sql()
                       ->prepareStatementForSqlObject( $select )
                       ->execute();

        foreach ( $result as $row )
        {
            $return[] = $this->selected( $row );
        }

        return $return;
    }

    /**
     * Get all elements as key => value pairs
     *
     * @param  array        $keyMapping
     * @param  mixed|null   $where
     * @param  mixed|null   $order
     * @param  int|null     $limit
     * @param  int|null     $offset
     * @return array
     */
    public function findOptions( array $keyMapping,
                                 $where     = null,
                                 $order     = null,
                                 $limit     = null,
                                 $offset    = null )
    {
        $columns = array();

        foreach ( $keyMapping as $key => $mapping )
        {
            if ( is_array( $mapping ) )
            {
                $columns = array_merge( $columns, $mapping );
            }
            else
            {
                $columns[] = $mapping;
            }
        }

        $select = $this->select( array_unique( $columns ) )
                       ->where( $where ?: array() )
                       ->order( $order ?: array() )
                       ->limit( $limit )
                       ->offset( $offset );

        /* @var $result \Zend\Db\Adapter\Driver\ResultInterface */

        $return = array();
        $result = $this->sql()
                       ->prepareStatementForSqlObject( $select )
                       ->execute();

        foreach ( $result as $row )
        {
            $option = array();

            foreach ( $keyMapping as $key => $mapping )
            {
                if ( is_numeric( $key ) )
                {
                    if ( is_array( $mapping ) )
                    {
                        $key = reset( $mapping );
                    }
                    else
                    {
                        $key = $mapping;
                    }
                }

                if ( is_array( $mapping ) )
                {
                    $value = null;

                    foreach ( $mapping as $field )
                    {
                        if ( ! empty( $row[$field] ) )
                        {
                            $value = $row[$field];
                            break;
                        }
                    }

                    $option[$key] = $value;
                }
                else
                {
                    $option[$key] = $row[$mapping];
                }
            }

            $return[] = $option;
        }

        return $return;
    }

    /**
     * Extract values from a structure
     *
     * @param object $structure
     * @return array
     */
    public function extract( $structure )
    {
        if ( $structure instanceof StructureAbstract )
        {
            return $structure->toArray();
        }

        if ( $structure instanceof Traversable )
        {
            return ArrayUtils::iteratorToArray( $structure );
        }

        return (array) $structure;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $structure
     * @return object
     */
    public function hydrate( array $data, $structure )
    {
        if ( $structure instanceof StructureAbstract )
        {
            $structure->setOptions( $data );
        }
        else
        {
            foreach ( $data as $key => $value )
            {
                $structure->$key = $value;
            }
        }

        if ( $structure instanceof MapperAwareInterface )
        {
            $structure->setMapper( $this );
        }

        return $structure;
    }

    /**
     * Get paginator
     *
     * @param   mixed|null  $where
     * @param   mixed|null  $order
     * @param   mixed|null  $columns
     * @param   mixed|null  $joins
     * @param   mixed|null  $quantifier
     * @return  \Zend\Paginator\Paginator
     */
    public function getPaginator( $where        = null,
                                  $order        = null,
                                  $columns      = null,
                                  $joins        = null,
                                  $quantifier   = null )
    {
        $select = $this->select( array_merge(
                            $this->getSelectColumns(),
                            (array) $columns
                        ) )
                       ->where( $where ?: array() )
                       ->order( $order ?: array() );

        if ( null !== $quantifier )
        {
            $select->quantifier( $quantifier );
        }

        if ( $joins instanceof Traversable )
        {
            $joins = ArrayUtils::iteratorToArray( $joins );
        }

        foreach ( (array) $joins as $table => $spec )
        {
            $select->join(
                isset( $spec['table'] )   ? $spec['table']   : $table,
                isset( $spec['where'] )   ? $spec['where']   : true,
                isset( $spec['columns'] ) ? $spec['columns'] : Select::SQL_STAR,
                isset( $spec['type'] )    ? $spec['type']    : Select::JOIN_INNER
            );
        }

        $resultSet = new HydratingResultSet(
            $this,
            $this->getStructurePrototype()
        );

        $adapter = new DbSelectPaginator(
            $select,
            $this->getDbAdapter(),
            $resultSet
        );

        return new Paginator( $adapter );
    }

    /**
     * Returns how many element structure from datasource
     * exists according to the conditions
     *
     * @param mixed $where
     * @return int
     */
    public function isExists( $where )
    {
        $select = $this->select( array(
                            'count' => new SqlExpression( 'COUNT(*)' )
                        ), false )
                        ->where( $where );

        $result = $this->sql()
                       ->prepareStatementForSqlObject( $select )
                       ->execute();

        $affected = $result->getAffectedRows();

        if ( $affected < 1 )
        // Just-in-case
        // @codeCoverageIgnoreStart
        {
            return null;
        }
        // @codeCoverageIgnoreEnd

        if ( $affected > 1 )
        {
            // Just-in-case
            // @codeCoverageIgnoreStart
            throw new \Zend\Db\Exception\UnexpectedValueException(
                'Too many rows'
            );
            // @codeCoverageIgnoreEnd
        }

        foreach ( $result as $row )
        {
            return (int) $row['count'];
        }
    }

    /**
     * Return bool from a Postgres-compliant boolean format
     *
     * @param mixed $value
     * @return bool
     * @codeCoverageIgnore
     */
    protected static function toBool( $value )
    {
        if ( is_null( $value ) )
        {
            return null;
        }

        if ( is_bool( $value ) )
        {
            return $value;
        }

        if ( is_float( $value ) || is_int( $value ) )
        {
            return $value != 0;
        }

        if ( is_array( $value ) )
        {
            return ! empty( $value );
        }

        if ( ! is_string( $value ) )
        {
            $value = (string) $value;
        }

        switch ( $value )
        {
            case '1':
            case 'y':
            case 'Y':
            case 't':
            case 'T':
            case 'on':
            case 'ON':
            case 'yes':
            case 'YES':
            case 'true':
            case 'TRUE':
                return true;

            case '':
            case '0':
            case 'n':
            case 'N':
            case 'f':
            case 'F':
            case 'no':
            case 'NO':
            case 'off':
            case 'OFF':
            case 'false':
            case 'FALSE':
                return false;

            default:
                return null;
        }
    }

    /**
     * Return Zend_Date object from a Postgres-compliant date
     *
     * @param mixed $value (accepts: int as unix-timestamp, string as ISO8601)
     * @return \DateTime
     */
    protected static function toDate( $value )
    {
        return new DateTime(
            is_int( $value )
                ? '@' . $value
                : (string) $value
        );
    }

    /**
     * Return array from a Postgres-compliant number-array format
     *
     * @param string $value
     * @param int $from [recursion param]
     * @param int $level [recursion param]
     * @param int $to [recursion param]
     * @return array
     */
    protected static function toNumberArray( $value,
            $from = 0, $level = 0, & $to = 0 )
    {
        $value = (string) $value;

        if ( 0 == $level )
        {
            $value = preg_replace( '/^(\[[0-9]+:[0-9]+\])+=/', '', $value );
        }

        $length = strlen( $value );
        $result = array();

        if ( '{' == $value[$from] )
        {
            $from++;

            $match = array();
            $index = 0;

            while ( '}' != $value[$from] )
            {
                if ( $from >= $length )
                // valid postgres array-literals never catch
                // @codeCoverageIgnoreStart
                {
                    break;
                }
                // @codeCoverageIgnoreEnd

                if ( '{' == $value[$from] )
                {
                    $result[$index] = static::toNumberArray(
                        $value, $from, $level + 1, $from
                    );

                    $from++;
                    $index++;

                    if ( ',' == $value[$from] )
                    {
                        $from++;
                    }
                }
                else if ( preg_match( '/([0-9]+(\\.[0-9]+)?|null|NULL)\s*,?\s*/',
                                      $value, $match, 0, $from ) )
                {
                    if ( strtolower( $match[1] ) == 'null' )
                    {
                        $result[$index] = null;
                    }
                    else
                    {
                        $result[$index] = floatval( $match[1] );
                    }

                    $from += strlen( $match[0] );
                    $index++;
                }
                else
                // valid postgres array-literals never catch
                // @codeCoverageIgnoreStart
                {
                    break;
                }
                // @codeCoverageIgnoreEnd
            }
        }

        $to = $from;
        return $result;
    }

}
