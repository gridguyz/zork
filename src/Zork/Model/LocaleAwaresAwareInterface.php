<?php

namespace Zork\Model;

/**
 * LocaleTrait
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface LocaleAwaresAwareInterface
{

    /**
     * Get locale-awares bound objects
     *
     * @return array
     */
    public function getLocaleAwares();

}
