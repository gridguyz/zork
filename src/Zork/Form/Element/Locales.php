<?php

namespace Zork\Form\Element;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zork\Form\TranslatorSettingsAwareInterface;

/**
 * Locale
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Locales extends MultiCheckboxGroup
           implements ServiceLocatorAwareInterface,
                      TranslatorSettingsAwareInterface
{

    use LocaleTrait;

    /**
     * Which text-domain should be used on translation
     *
     * @var string|null
     */
    protected $translatorTextDomain = 'locale';

}
