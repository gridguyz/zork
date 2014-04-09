<?php

namespace Zork\Form\Element;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CssFontStyle extends AbstractSelectDefaultOptions
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
        'normal'    => 'css.font-style.normal',
        'italic'    => 'css.font-style.italic',
        'oblique'   => 'css.font-style.oblique',
    );

}
