<?php

namespace Zork\Mvc\Controller;

/**
 * LocaleSelectorInterface
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface LocaleSelectorInterface
{
    
    /**
     * @return null|array of available locales
     */
    public function getAvailableLocales();
    
}
