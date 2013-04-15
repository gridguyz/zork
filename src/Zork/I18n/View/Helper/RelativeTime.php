<?php

namespace Zork\I18n\View\Helper;

use DateTime as IntlDateTime;
use Zend\View\Helper\AbstractHelper;

/**
 * RelativeTime
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class RelativeTime extends AbstractHelper
{

    /**
     * @var int
     */
    const DEFAULT_FROM = 'now';

    /**
     * @var \DateTime
     */
    protected $from;

    /**
     * @return \DateTime
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param   null|int|string|\DateTime   $dateFormat
     * @return  \Zork\I18n\View\Helper\RelativeTime
     */
    public function setFrom( $from )
    {
        if ( empty( $from ) )
        {
            $from = static::DEFAULT_FROM;
        }

        if ( is_numeric( $from ) )
        {
            $from = new IntlDateTime( '@' . $from );
        }
        else if ( ! $from instanceof IntlDateTime )
        {
            $from = new IntlDateTime( $from );
        }

        $this->from = $from;
        return $this;
    }

    /**
     * @param   null|int|string|\DateTime   $from
     */
    public function __construct( $from = null )
    {
        $this->setFrom( $from );
    }

    /**
     * Display a single value
     *
     * @param null|int|string|\DateTime $value
     * @return string
     */
    public function __invoke( $value = null, $from = null )
    {
        if ( null === $value )
        {
            return $this;
        }

        if ( null === $from )
        {
            $from = $this->getFrom();
        }

        if ( is_numeric( $value ) )
        {
            $value = new IntlDateTime( '@' . $value );
        }
        else if ( ! $value instanceof IntlDateTime )
        {
            $value = new IntlDateTime( $value );
        }

        $diff = $value->diff( $from );
        $post = $diff->invert ? '.fromNow' : '.ago';

        switch ( true )
        {
            case $diff->y > 1:

                return sprintf(
                    $this->getView()
                         ->translate( 'default.years.%d' . $post, 'default' ),
                    round( $diff->y + $diff->m / 12 )
                );

            case $diff->y == 1 || $diff->m > 1:

                return sprintf(
                    $this->getView()
                         ->translate( 'default.months.%d' . $post, 'default' ),
                    round( $diff->y * 12 + $diff->m + $diff->d / 30 )
                );

            case $diff->m == 1 || $diff->d > 1:

                return sprintf(
                    $this->getView()
                         ->translate( 'default.days.%d' . $post, 'default' ),
                    $diff->days
                );

            case $diff->d == 1 || $diff->h > 1:

                return sprintf(
                    $this->getView()
                         ->translate( 'default.hours.%d' . $post, 'default' ),
                    round( $diff->d * 24 + $diff->h + $diff->i / 60 )
                );

            case $diff->h == 1 || $diff->i > 1:

                return sprintf(
                    $this->getView()
                         ->translate( 'default.minutes.%d' . $post, 'default' ),
                    round( $diff->h * 60 + $diff->i + $diff->s / 60 )
                );

            case $diff->i == 1 || $diff->s > 1:

                return sprintf(
                    $this->getView()
                         ->translate( 'default.seconds.%d' . $post, 'default' ),
                    $diff->i * 60 + $diff->s
                );

            default:

                return $this->getView()
                            ->translate( 'default.justNow' );
        }
    }

}
