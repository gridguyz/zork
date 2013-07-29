<?php

namespace Zork\I18n\Translator;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\I18n\Translator\TranslatorServiceFactory as ZendTranslatorServiceFactory;

/**
 * TranslatorFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TranslatorServiceFactory extends ZendTranslatorServiceFactory
{

    /**
     * Create translator service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zend\I18n\Translator\Translator
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        /* @var $translator \Zork\I18n\Translator\Translator */
        $locale     = $serviceLocator->get( 'Locale' );
        $config     = $serviceLocator->get( 'Config' );
        $trConfig   = isset( $config['translator'] ) ? $config['translator'] : array();
        $translator = Translator::factory( $trConfig );
        $translator->setFallbackLocale( $locale->getFallback() );
        return $translator;
    }

}
