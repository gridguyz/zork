<?php

namespace Zork\Form\Element;

use Locale as IntlLocale;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * LocaleTrait
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @implements \Zend\ServiceManager\ServiceLocatorAwareInterface
 */
trait LocaleTrait
{

    /**
     * Container of service locator.
     *
     * @var type
     */
    protected $serviceLocator;

    /**
     * True: show only enabled locales
     *
     * @var bool
     */
    protected $onlyEnabledLocales = true;

    /**
     * Setter of service locator
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zork\Form\Element\Locale
     */
    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Getter of service locator
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @return array
     */
    public function getValueOptions()
    {
        if ( empty( $this->valueOptions ) )
        {
            $this->valueOptions = array();
            $enabledLocales     = $this->getServiceLocator()
                                       ->get( 'Locale' )
                                       ->getAvailableFlags();

            if ( $this->onlyEnabledLocales )
            {
                $enabledLocales = array_filter( $enabledLocales );
            }

            foreach ( $enabledLocales as $locale => $enabled )
            {
                $main       = IntlLocale::getPrimaryLanguage( $locale );
                $sub        = IntlLocale::getRegion( $locale );
                $mainKey    = 'locale.main.' . $main;
                $localeKey  = 'locale.sub.' . $locale;

                if ( ! empty( $this->valueOptions[$mainKey] ) )
                {
                    $this->valueOptions[$mainKey]['options'][$locale] = $localeKey;
                }
                elseif ( ! $sub && empty( $this->valueOptions[$locale] ) )
                {
                    $this->valueOptions[$locale] = $mainKey;
                }
                else
                {
                    if ( $sub && ! empty( $this->valueOptions[$main] ) )
                    {
                        $this->valueOptions[$mainKey]['options'] = array(
                            $main => 'locale.sub.' . $main,
                        );

                        unset( $this->valueOptions[$main] );
                    }

                    $this->valueOptions[$mainKey]['options'][$locale] = $localeKey;
                    $this->valueOptions[$mainKey]['label'] = $mainKey;
                }
            }

            foreach ( $this->valueOptions as & $valueGroup )
            {
                if ( is_array( $valueGroup ) &&
                     isset( $valueGroup['options'] ) &&
                     is_array( $valueGroup['options'] ) )
                {
                    ksort( $valueGroup['options'] );
                }
            }

            $first = reset( $this->valueOptions );

            if ( count( $this->valueOptions ) === 1 &&
                 is_array( $first ) && ! empty( $first['options'] ) )
            {
                $this->valueOptions = $first['options'];
            }
        }

        return parent::getValueOptions();
    }

    /**
     * Set options for an element. Accepted options are:
     * - label: label to associate with the element
     * - label_attributes: attributes to use when the label is rendered
     * - value_options: list of values and labels for the select options
     * - empty_option: should an empty option be prepended to the options ?
     *
     * @param  array|\Traversable $options
     * @return RadioGroup|ElementInterface
     * @throws InvalidArgumentException
     */
    public function setOptions($options)
    {
        parent::setOptions( $options );

        if ( isset( $this->options['only_enabled'] ) )
        {
            $this->onlyEnabledLocales = (bool) $this->options['only_enabled'];
        }

        return $this;
    }

}
