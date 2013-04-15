<?php

namespace Zork\Mvc\Controller\Plugin;

/**
 * MiddleLayoutInterface
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface MiddleLayoutInterface
{
    
    /**
     * @return string
     */
    public function getTemplate();
    
    /**
     * @return array
     */
    public function getVariables();
    
}
