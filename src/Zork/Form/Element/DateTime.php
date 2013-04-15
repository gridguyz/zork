<?php

namespace Zork\Form\Element;

use DateInterval;
use Zend\Form\Element\DateTime as ElementBase;
use Zend\InputFilter\InputProviderInterface;
use Zork\Form\TranslatorSettingsAwareInterface;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DateTime extends ElementBase
            implements InputProviderInterface,
                       TranslatorSettingsAwareInterface
{

    use InputProviderTrait;

    /**
     * @const string
     */
    const DATETIME_FORMAT = 'Y-m-d\TH:i:sP';

    /**
     * @var string
     */
    protected $format = self::DATETIME_FORMAT;

    /**
     * Retrieves a DateStep Validator configured for a DateTime Input type
     *
     * @return DateTime
     */
    protected function getStepValidator()
    {
        $stepValidator  = parent::getStepValidator();
        $baseValue      = $stepValidator->getBaseValue();
        $stepValue      = ( isset( $this->attributes['step'] ) )
                        ? (int) $this->attributes['step'] : null; // Seconds

        if ( $baseValue === '1970-01-01T00:00Z' ||
             $baseValue === '1970-01-01T00:00:00Z' )
        {
            $stepValidator->setBaseValue( 0 );
        }

        return $stepValidator->setStep(
            new DateInterval( 'PT' . ( $stepValue ?: 1 ) . 'S' )
        );
    }

}
