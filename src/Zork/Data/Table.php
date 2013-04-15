<?php

namespace Zork\Data;

use Iterator;
use Traversable;
use ArrayAccess;
use ArrayIterator;
use OuterIterator;
use IteratorAggregate;

/**
 * Table
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Table implements OuterIterator,
                       TabularInterface
{

    /**
     * @const string
     */
    const STRING    = 'Zork\Data\Transform::toString';

    /**
     * @const string
     */
    const INTEGER   = 'Zork\Data\Transform::toInteger';

    /**
     * @const string
     */
    const FLOAT     = 'Zork\Data\Transform::toFloat';

    /**
     * @const string
     */
    const BOOLEAN   = 'Zork\Data\Transform::toBoolean';

    /**
     * @const string
     */
    const DATETIME  = 'Zork\Data\Transform::toDateTime';

    /**
     * @var Iterator
     */
    protected $iterator;

    /**
     * @var array
     */
    protected $fields = array();

    /**
     * Construct table
     *
     * @param   array|Traversable $source
     * @param   array|Traversable $fields
     */
    public function __construct( $source, $fields )
    {
        if ( is_array( $source ) )
        {
            $source = new ArrayIterator( $source );
        }

        while ( $source instanceof IteratorAggregate )
        {
            $source = $source->getIterator();
        }

        if ( ! $source instanceof Iterator )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                '%s: $source must be an array, or an instance of \Traversable',
                __METHOD__
            ) );
        }

        $this->iterator = $source;
        foreach ( $fields as $name => $type )
        {
            $this->fields[$name] = Transform::toCallable( $type );
        }
    }

    /**
     * Get field names
     *
     * @return  array
     */
    public function getFieldNames()
    {
        return array_keys( $this->fields );
    }

    /**
     * Extract a field, from data
     *
     * @param   array|object    $data
     * @param   string          $name
     * @return  mixed
     */
    protected function extractField( $data, $name )
    {
        if ( is_object( $data ) && method_exists( $data, 'getOption' ) )
        {
            return $data->$name ?: $data->getOption( $name );
        }
        else if ( is_array( $data ) || $data instanceof ArrayAccess )
        {
            return $data[$name];
        }
        else
        {
            return $data->$name;
        }
    }

    /**
     * Retrieve an external iterator
     *
     * @return  Iterator
     */
    public function getInnerIterator()
    {
        return $this->iterator;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        return $this->iterator->rewind();
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * Return the key of the current element
     *
     * @return scalar scalar on success, or <b>NULL</b> on failure.
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        return $this->iterator->next();
    }

    /**
     * Return the current element
     *
     * @return array Could return any type.
     */
    public function current()
    {
        $current    = $this->iterator->current();
        $result     = array();

        foreach ( $this->fields as $field => $cb )
        {
            $value = $this->extractField( $current, $field );
            $result[$field] = $value === null
                ? null
                : call_user_func( $cb, $value, $current );
        }

        return $result;
    }

    /**
     * Export data to a file
     *
     * @param   string      $type
     * @param   array|null  $options
     * @return  \Zork\Data\FileInterface
     * @throws  Exception\InvalidArgumentException
     */
    public function export( $type, $options = null )
    {
        if ( class_exists( $type ) )
        {
            $class = $type;
        }
        else if ( ! class_exists( $class = __NAMESPACE__ . '\\Export\\' . ucfirst( $type ) ) )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                '%s: $type "%s" is not a valid class "%s"',
                __METHOD__,
                $type,
                $class
            ) );
        }

        return new $class( $this, $options );
    }

}
