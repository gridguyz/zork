<?php

namespace Zork\Form;

/**
 * PrepareElementsAwareInterface
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface PrepareElementsAwareInterface
{
    
    /**
     * Prepare additional elements for the form
     * 
     * @return void
     */
    public function prepareElements();
    
}
