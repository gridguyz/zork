<?php

namespace Zork\Db\Sql\Expression;

use Zend\Db\Sql\Expression;

/**
 * Cast
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Cast extends Expression
{

    /**
     * @var string
     */
    const CAST_NUMERIC = 'NUMERIC';

    /**
     * @var string
     */
    const CAST_DECIMAL = 'DECIMAL';

    /**
     * @var string
     */
    const CAST_INTEGER = 'INTEGER';

    /**
     * @var string
     */
    const CAST_SIGNED = 'SIGNED';

    /**
     * @var string
     */
    const CAST_UNSIGNED = 'UNSIGNED';

    /**
     * @var string
     */
    const CAST_REAL = 'REAL';

    /**
     * @var string
     */
    const CAST_DOUBLE_PRECISION = 'DOUBLE PRECISION';

    /**
     * @var string
     */
    const CAST_CHARACTER = 'CHARACTER';

    /**
     * @var string
     */
    const CAST_CHARACTER_VARYING = 'CHARACTER VARYING';

    /**
     * @var string
     */
    const CAST_TEXT = 'TEXT';

    /**
     * @var string
     */
    const CAST_BINARY = 'BINARY';

    /**
     * @var string
     */
    const CAST_DATE = 'DATE';

    /**
     * @var string
     */
    const CAST_TIME = 'TIME';

    /**
     * @var string
     */
    const CAST_DATETIME = 'DATETIME';

    /**
     * @var string
     */
    const CAST_TIMESTAMP = 'TIMESTAMP';

    /**
     * @var string
     */
    const CAST_TIMESTAMP_WITH_TIME_ZONE = 'TIMESTAMP WITH TIME ZONE';

    /**
     * @var string
     */
    const CAST_BOOLEAN = 'BOOLEAN';

    /**
     * @var string
     */
    const CAST_NUMERIC_ARRAY = 'NUMERIC ARRAY';

    /**
     * @var string
     */
    const CAST_DECIMAL_ARRAY = 'DECIMAL ARRAY';

    /**
     * @var string
     */
    const CAST_INTEGER_ARRAY = 'INTEGER ARRAY';

    /**
     * @var string
     */
    const CAST_SIGNED_ARRAY = 'SIGNED ARRAY';

    /**
     * @var string
     */
    const CAST_UNSIGNED_ARRAY = 'UNSIGNED ARRAY';

    /**
     * @var string
     */
    const CAST_REAL_ARRAY = 'REAL ARRAY';

    /**
     * @var string
     */
    const CAST_DOUBLE_PRECISION_ARRAY = 'DOUBLE PRECISION ARRAY';

    /**
     * @var string
     */
    const CAST_CHARACTER_ARRAY = 'CHARACTER ARRAY';

    /**
     * @var string
     */
    const CAST_CHARACTER_VARYING_ARRAY = 'CHARACTER VARYING ARRAY';

    /**
     * @var string
     */
    const CAST_TEXT_ARRAY = 'TEXT ARRAY';

    /**
     * @var string
     */
    const CAST_BINARY_ARRAY = 'BINARY ARRAY';

    /**
     * @var string
     */
    const CAST_DATE_ARRAY = 'DATE ARRAY';

    /**
     * @var string
     */
    const CAST_TIME_ARRAY = 'TIME ARRAY';

    /**
     * @var string
     */
    const CAST_DATETIME_ARRAY = 'DATETIME ARRAY';

    /**
     * @var string
     */
    const CAST_TIMESTAMP_ARRAY = 'TIMESTAMP ARRAY';

    /**
     * @var string
     */
    const CAST_TIMESTAMP_WITH_TIME_ZONE_ARRAY = 'TIMESTAMP WITH TIME ZONE ARRAY';

    /**
     * @var string
     */
    const CAST_BOOLEAN_ARRAY = 'BOOLEAN ARRAY';

    /**
     * Cast expression
     *
     * @param   mixed   $value
     * @param   string  $cast
     * @param   bool    $escapeCast
     * @param   string  $type
     */
    public function __construct( $value, $cast, $escapeCast = false, $type = self::TYPE_VALUE )
    {
        $parameters = array( $value );
        $types      = array( $type );
        $expression = sprintf(
            'CAST( %s AS %s )',
            self::PLACEHOLDER,
            $escapeCast ? self::PLACEHOLDER : $cast
        );

        if ( $escapeCast )
        {
            $parameters[]   = $cast;
            $types[]        = self::TYPE_IDENTIFIER;
        }

        parent::__construct( $expression, $parameters, $types );
    }

}
