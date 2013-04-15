<?php

namespace Zork\Form;

/**
 * TranslatorTextDomainAwareInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface TranslatorSettingsAwareInterface
{

    /**
     * @return bool
     */
    public function isTranslatorEnabled();

    /**
     * @return string
     */
    public function getTranslatorTextDomain();

}
