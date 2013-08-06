<?php

namespace Zork\I18n\View\Helper;

/**
 * Date
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Date extends DateTime
{

    /**
     * @var int
     */
    protected $timeFormat = self::NONE;

    /**
     * @param   null|int    $dateFormat
     */
    public function __construct( $dateFormat = null )
    {
        parent::__construct( $dateFormat );
    }

}
