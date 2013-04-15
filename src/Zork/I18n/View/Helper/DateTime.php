<?php

namespace Zork\I18n\View\Helper;

use IntlDateFormatter;
use DateTime as IntlDateTime;
use Zend\View\Helper\AbstractHelper;

/**
 * DateTime
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DateTime extends AbstractHelper
{

    /**
     * @var int
     */
    const FULL      = IntlDateFormatter::FULL;

    /**
     * @var int
     */
    const LONG      = IntlDateFormatter::LONG;

    /**
     * @var int
     */
    const MEDIUM    = IntlDateFormatter::MEDIUM;

    /**
     * @var int
     */
    const SHORT     = IntlDateFormatter::SHORT;

    /**
     * @var int
     */
    const NONE      = IntlDateFormatter::NONE;

    /**
     * @var int
     */
    protected $dateFormat   = self::LONG;

    /**
     * @var int
     */
    protected $timeFormat   = self::LONG;

    /**
     * @return int
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * @return int
     */
    public function getTimeFormat()
    {
        return $this->timeFormat;
    }

    /**
     * @param int $dateFormat
     * @return \Core\View\Helper\RowSet\Type\DateTime
     */
    public function setDateFormat( $dateFormat )
    {
        $this->dateFormat = (int) $dateFormat;
        return $this;
    }

    /**
     * @param int $timeFormat
     * @return \Core\View\Helper\RowSet\Type\DateTime
     */
    public function setTimeFormat( $timeFormat )
    {
        $this->timeFormat = (int) $timeFormat;
        return $this;
    }

    /**
     * @param null|int $dateFormat
     * @param null|int $timeFormat
     */
    public function __construct( $dateFormat = null, $timeFormat = null )
    {
        if ( null !== $dateFormat )
        {
            $this->setDateFormat( $dateFormat );
        }

        if ( null !== $timeFormat )
        {
            $this->setTimeFormat( $timeFormat );
        }
    }

    /**
     * Display a single value
     *
     * @param null|int|string|\DateTime $value
     * @return string
     */
    public function __invoke( $value = null )
    {
        if ( null === $value )
        {
            return $this;
        }

        if ( ! $value instanceof IntlDateTime )
        {
            if ( is_numeric( $value ) )
            {
                $value = new IntlDateTime( '@' . $value );
            }
            else
            {
                $value = new IntlDateTime( $value );
            }
        }

        return $this->view->dateFormat(
            $value,
            $this->dateFormat,
            $this->timeFormat
        );
    }

}
