<?php

namespace Zork\Form\View;

/**
 * HelperAwareInterface
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface HelperAwareInterface
{
    
    /**
     * @return string
     */
    public function getRendererHelperName();
    
}
