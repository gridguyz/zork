<?php

namespace Zork\Form\Element;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CssBorderStyle extends AbstractSelectDefaultOptions
{

    /**
     * Which text-domain should be used on translation
     *
     * @var string|null
     */
    protected $translatorTextDomain = 'css';

    /**
     * @var array
     */
    protected $defaultValueOptions = array(
        ''          => 'css.default',
        'none'      => 'css.border-style.none',
        'solid'     => 'css.border-style.solid',
        'dotted'    => 'css.border-style.dotted',
        'dashed'    => 'css.border-style.dashed',
        'double'    => 'css.border-style.double',
        'ridge'     => 'css.border-style.ridge',
        'groove'    => 'css.border-style.groove',
        'inset'     => 'css.border-style.inset',
        'outset'    => 'css.border-style.outset',
    );

}
