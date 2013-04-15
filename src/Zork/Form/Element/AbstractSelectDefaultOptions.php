<?php

namespace Zork\Form\Element;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AbstractSelectDefaultOptions extends Select
{

    /**
     * @var array
     * @abstract
     */
    protected $defaultValueOptions = array();

    /**
     * @return array
     */
    public function getValueOptions()
    {
        if ( empty( $this->valueOptions ) )
        {
            $this->valueOptions = $this->defaultValueOptions;
        }

        return parent::getValueOptions();
    }

}
