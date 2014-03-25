<?php

namespace Zork\Validator\Translator;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * TranslatorServiceFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TranslatorServiceFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        return new I18nTranslatorGateway(
            $serviceLocator->get( 'translator' )
        );
    }

}
