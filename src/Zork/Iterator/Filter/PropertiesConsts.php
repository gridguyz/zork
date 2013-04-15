<?php

namespace Zork\Iterator\Filter;

/**
 * PropertiesConst
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface PropertiesConsts
{

    /**
     * @var string
     */
    const CMP_EQUAL         = '==';

    /**
     * @var string
     */
    const CMP_IDENTICAL     = '===';

    /**
     * @var string
     */
    const CMP_REGEXP        = '~~';

    /**
     * @var string
     */
    const CMP_NOT_EQUAL     = '!=';

    /**
     * @var string
     */
    const CMP_NOT_IDENTICAL = '!==';

    /**
     * @var string
     */
    const CMP_NOT_REGEXP    = '!~';

    /**
     * @var string
     */
    const CMP_GREATER_THAN  = '>';

    /**
     * @var string
     */
    const CMP_GREATER_EQUAL = '>=';

    /**
     * @var string
     */
    const CMP_LESSER_THAN   = '<';

    /**
     * @var string
     */
    const CMP_LESSER_EQUAL  = '<=';

    /**
     * @var string
     */
    const CMP_CALLBACK      = '()';

    /**
     * @var string
     */
    const CMP_VALID_CHARS   = '!<=>~()';

    /**
     * @var string
     */
    const CMP_DEFAULT       = self::CMP_EQUAL;

    /**
     * @var string
     */
    const CMP_EQ            = self::CMP_EQUAL;

    /**
     * @var string
     */
    const CMP_ID            = self::CMP_IDENTICAL;

    /**
     * @var string
     */
    const CMP_RE            = self::CMP_REGEXP;

    /**
     * @var string
     */
    const CMP_NE            = self::CMP_NOT_EQUAL;

    /**
     * @var string
     */
    const CMP_NI            = self::CMP_NOT_IDENTICAL;

    /**
     * @var string
     */
    const CMP_NR            = self::CMP_NOT_REGEXP;

    /**
     * @var string
     */
    const CMP_GT            = self::CMP_GREATER_THAN;

    /**
     * @var string
     */
    const CMP_GE            = self::CMP_GREATER_EQUAL;

    /**
     * @var string
     */
    const CMP_LT            = self::CMP_LESSER_THAN;

    /**
     * @var string
     */
    const CMP_LE            = self::CMP_LESSER_EQUAL;

    /**
     * @var string
     */
    const CMP_CB            = self::CMP_CALLBACK;

}
