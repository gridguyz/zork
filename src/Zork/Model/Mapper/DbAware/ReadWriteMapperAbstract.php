<?php

namespace Zork\Model\Mapper\DbAware;

use Zend\Stdlib\ArrayUtils;
use Zork\Db\Sql\TableIdentifier;
use Zend\Db\Sql\Expression as SqlExpression;
use Zend\Db\Exception\ExceptionInterface as DbException;
use Zork\Model\Structure\StructureAbstract;
use Zork\Model\Mapper\ReadWriteMapperInterface;

/**
 * ReadWriteMapperAbstract
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class ReadWriteMapperAbstract
       extends ReadOnlyMapperAbstract
    implements ReadWriteMapperInterface
{

    /**
     * Integer type-converters for selected(), deselect()
     * @var array
     */
    const INT           = 'intval;strval';

    /**
     * Integer type-converters for selected(), deselect()
     * @var array
     */
    const INTEGER       = self::INT;

    /**
     * Boolean type-converters for selected(), deselect()
     * @var array
     */
    const BOOL          = 'self::toBool;self::fromBool';

    /**
     * Boolean type-converters for selected(), deselect()
     * @var array
     */
    const BOOLEAN       = self::BOOL;

    /**
     * Float type-converters for selected(), deselect()
     * @var array
     */
    const FLOAT         = 'floatval;strval';

    /**
     * Double (float) type-converters for selected(), deselect()
     * @var array
     */
    const DOUBLE        = self::FLOAT;

    /**
     * Real (float) type-converters for selected(), deselect()
     * @var array
     */
    const REAL          = self::FLOAT;

    /**
     * String type-converters for selected(), deselect()
     * @var array
     */
    const STR           = 'strval;strval';

    /**
     * String type-converters for selected(), deselect()
     * @var array
     */
    const STRING        = self::STR;

    /**
     * Text (string) type-converters for selected(), deselect()
     * @var array
     */
    const TEXT          = self::STR;

    /**
     * Char (string) type-converters for selected(), deselect()
     * @var array
     */
    const CHAR          = self::STR;

    /**
     * Varchar (string) type-converters for selected(), deselect()
     * @var array
     */
    const VARCHAR       = self::STR;

    /**
     * Date type-converters for selected(), deselect()
     * @var array
     */
    const DATE          = 'self::toDate;self::fromDate';

    /**
     * Date-time type-converters for selected(), deselect()
     * (use time-zones)
     * @var array
     */
    const DATETIME      = self::DATE;

    /**
     * Number-array type-converters for selected(), deselect()
     * @var array
     */
    const NUMARRAY      = 'self::toNumberArray;self::fromNumberArray';

    /**
     * Number-array type-converters for selected(), deselect()
     * @var array
     */
    const NUMBERARRAY   = self::NUMARRAY;

    /**
     * Integer-array type-converters for selected(), deselect()
     * @var array
     */
    const INTARRAY      = self::NUMARRAY;

    /**
     * Integer-array type-converters for selected(), deselect()
     * @var array
     */
    const INTEGERARRAY  = self::NUMARRAY;

    /**
     * Float-array type-converters for selected(), deselect()
     * @var array
     */
    const FLOATARRAY    = self::NUMARRAY;

    /**
     * Double-(float)-array type-converters for selected(), deselect()
     * @var array
     */
    const DOUBLEARRAY   = self::NUMARRAY;

    /**
     * Real-(float)-array type-converters for selected(), deselect()
     * @var array
     */
    const REALARRAY     = self::NUMARRAY;

    /**
     * Use sql's "DEFAULT" keyword on
     * save if value is NULL
     *
     * @var array
     * @abstract
     */
    protected static $useDefaultOnSave = array();

    /**
     * Transform insert/update data from structure
     *
     * @param array|\Zork\Model\Structure\StructureAbstract $structure
     * @return array
     */
    public function deselect( & $structure )
    {
        $data       = array();
        $columns    = static::getColumns();

        foreach ( $structure as $key => $value )
        {
            if ( isset( $columns[$key] ) )
            {
                $data[$key] = $value;
            }
        }

        foreach ( $columns as $key => $transformators )
        {
            if ( ! is_array( $transformators ) )
            {
                $transformators = explode( ';', $transformators );
            }

            if ( ! empty( $transformators[1] ) )
            {
                if ( isset( $data[$key] ) && ! is_null( $data[$key] ) )
                {
                    $data[$key] = call_user_func(
                        $transformators[1],
                        $data[$key]
                    );
                }
            }
        }

        foreach ( $data as $key => $value )
        {
            if ( is_array( $value ) || (
                    is_object( $value ) && ! ( $value instanceof SqlExpression )
                ) )
            // Safety mechanism
            // @codeCoverageIgnoreStart
            {
                unset( $data[$key] );
            }
            // @codeCoverageIgnoreEnd
        }

        return $data;
    }

    /**
     * Create structure from plain data
     *
     * @param array|\Traversable $data
     * @return \Zork\Model\Structure\StructureAbstract
     */
    public function create( $data )
    {
        $data = ArrayUtils::iteratorToArray( $data );
        return $this->createStructure( $data );
    }

    /**
     * Insert element structure to datasource
     *
     * @param array $structure
     * @return int Number of affected rows
     */
    protected function insert( & $data )
    {
        $insert = $this->sql()
                       ->insert()
                       ->values( $data );

        return $this->sql()
                    ->prepareStatementForSqlObject( $insert )
                    ->execute()
                    ->getAffectedRows();
    }

    /**
     * Update element structure to datasource
     *
     * @param array $data
     * @return int Number of affected rows
     */
    protected function update( & $data )
    {
        $where  = array();
        $udata  = $data;

        foreach ( static::getPrimaryKeys() as $key )
        {
            $where[ $key ] = $udata[$key];
            unset( $udata[$key] );
        }

        $update = $this->sql()
                       ->update()
                       ->set( $udata )
                       ->where( $where );

        return $this->sql()
                    ->prepareStatementForSqlObject( $update )
                    ->execute()
                    ->getAffectedRows();
    }

    /**
     * Save element structure to datasource
     *
     * @param array|\Zork\Model\Structure\StructureAbstract $structure
     * @return int Number of affected rows
     */
    public function save( & $structure )
    {
        $update     = true;
        $data       = $this->deselect( $structure );
        $primaries  = static::getPrimaryKeys();

        foreach ( $primaries as $key )
        {
            if ( empty( $data[$key] ) )
            {
                $update = false;
                break;
            }
        }

        if ( ! empty( static::$useDefaultOnSave ) )
        {
            $default = new SqlExpression( 'DEFAULT' );

            foreach ( static::$useDefaultOnSave as $column => $use )
            {
                if ( $use && array_key_exists( $column, $data ) &&
                     null === $data[$column] )
                {
                    $data[$column] = clone $default;
                }
            }
        }

        if ( ! $update || ! ( $rows = $this->update( $data ) ) )
        {
            foreach ( $primaries as $key )
            {
                if ( empty( $data[$key] ) )
                {
                    unset( $data[$key] );
                }
            }

            $db   = $this->getDbAdapter();
            $rows = $this->insert( $data );

            foreach ( $primaries as $key )
            {
                if ( empty( $data[$key] ) )
                {
                    try
                    {
                        $table = $this->getTableInSchema(
                            isset( static::$sequences[$key] )
                                ? static::$sequences[$key]
                                : static::$tableName .
                                  '_' . $key . '_seq'
                        );

                        if ( $table instanceof TableIdentifier )
                        {
                            $table = $db->getPlatform()
                                        ->quoteIdentifierChain(
                                            $table->getIdentifierChain()
                                        );
                        }
                        else
                        {
                            $table = $db->getPlatform()
                                        ->quoteIdentifier( $table );
                        }

                        $id = $db->getDriver()
                                 ->getConnection()
                                 ->getLastGeneratedValue( $table );
                    }
                    catch ( DbException $e )
                    // if id not found, its a design error
                    // @codeCoverageIgnoreStart
                    {
                        $id = false;
                    }
                    // @codeCoverageIgnoreEnd

                    if ( $id )
                    {
                        if ( is_array( $structure ) )
                        {
                            $structure[ $key ] = $id;
                        }
                        else if ( $structure instanceof StructureAbstract )
                        {
                            $structure->setOption( $key, $id );
                        }
                        else
                        {
                            $structure->$key = $id;
                        }
                    }
                }
            }
        }

        return $rows;
    }

    /**
     * Delete element structure from datasource
     *
     * @param int|array|\Zork\Model\Structure\StructureAbstract
     *        $structureOrPrimaryKeys Primary id(s)
     * @return int Number of affected rows
     */
    public function delete( $structureOrPrimaryKeys )
    {
        $where      = array();
        $primaries  = static::getPrimaryKeys();
        $update     = $structureOrPrimaryKeys;

        if ( is_object( $structureOrPrimaryKeys ) )
        {
            $data = array();

            foreach ( $primaries as $index => $primary )
            {
                $data[ $index ] = $structureOrPrimaryKeys->$primary;
            }

            $structureOrPrimaryKeys = $data;
        }
        else if ( is_array( $structureOrPrimaryKeys ) )
        {
            $data = array();

            foreach ( $structureOrPrimaryKeys as $index => $value )
            {
                if ( is_numeric( $index ) )
                {
                    $update = null;
                    $data[ $index ] = $value;
                }
                else if ( in_array( $index, $primaries ) )
                {
                    $data[ array_search( $index, $primaries ) ] = $value;
                }
            }

            $structureOrPrimaryKeys = $data;
        }
        else
        {
            $update = null;
            $structureOrPrimaryKeys = func_get_args();
        }

        foreach ( $primaries as $index => $primary )
        {
            $where[ $primary ] = $structureOrPrimaryKeys[ $index ];

            if ( null !== $update )
            {
                if ( is_array( $update ) )
                {
                    $update[ $primary ] = null;
                }
                else if ( $update instanceof StructureAbstract )
                {
                    $update->setOption( $primary, null );
                }
                else
                {
                    $update->$primary = null;
                }
            }
        }

        $delete = $this->sql()
                       ->delete()
                       ->where( $where );

        $result = $this->sql()
                       ->prepareStatementForSqlObject( $delete )
                       ->execute();

        return $result->getAffectedRows();
    }

    /**
     * Return the Postgres-compliant boolean format from a bool
     *
     * @param bool $value
     * @return string
     */
    protected static function fromBool( $value )
    {
        return $value ? 't' : 'f';
    }

    /**
     * Return the Postgres-compliant date format from a Zend_Date object
     *
     * @param \DateTime $value
     * @return string
     */
    protected static function fromDate( $value )
    {
        if ( $value instanceof \DateTime )
        {
            return $value->format( \DateTime::ISO8601 );
        }
        else
        {
            return (string) $value;
        }
    }

    /**
     * Return the Postgres-compliant integer-array format
     *
     * @param array $value
     * @return string
     */
    protected static function fromNumberArray( $value )
    {
        $result = '';

        if ( ! empty( $value ) )
        {
            foreach ( (array) $value as $item )
            {
                if ( ! empty( $result ) )
                {
                    $result .= ', ';
                }

                switch ( true )
                {
                    case null === $item:
                        $result .= 'NULL';
                        break;

                    case is_array( $item ):
                        $result .= static::fromNumberArray( $item );
                        break;

                    default:
                        $result .= floatval( $item );
                        break;
                }
            }
        }

        return '{' . $result . '}';
    }

}
