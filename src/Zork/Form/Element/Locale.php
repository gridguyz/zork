<?php

namespace Zork\Form\Element;

use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Locale
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Locale extends Select
          implements ServiceLocatorAwareInterface
{

    use LocaleTrait;

    /**
     * Which text-domain should be used on translation
     *
     * @var string|null
     */
    protected $translatorTextDomain = 'locale';

    /**
     * Retrieve the element value
     *
     * @return mixed
     */
    public function getValue()
    {
        if ( empty( $this->value ) &&
             ! empty( $this->attributes['required'] ) )
        {
            $locale     = $this->getServiceLocator()
                               ->get( 'Locale' );
            $available  = $locale->getAvailableFlags();

            if ( $this->onlyEnabledLocales )
            {
                $available = array_filter( $available );
            }

            if ( isset( $available[$current = $locale->getCurrent()] ) )
            {
                $this->value = $current;
            }
            else if ( isset( $available[$default = $locale->getDefault()] ) )
            {
                $this->value = $default;
            }
            else if ( isset( $available[$fallback = $locale->getFallback()] ) )
            {
                $this->value = $fallback;
            }
        }

        return parent::getValue();
    }

}
