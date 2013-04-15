<?php

namespace Zork\Form\Element;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CssTextAlign extends AbstractSelectDefaultOptions
{

    /**
     * Which text-domain should be used on translation
     *
     * @var string|null
     */
    protected $translatorTextDomain = 'default';

    /**
     * @var array
     */
    protected $defaultValueOptions = array(
        ''          => 'css.default',
        'left'      => 'css.text-align.left',
        'center'    => 'css.text-align.center',
        'right'     => 'css.text-align.right',
        'justify'   => 'css.text-align.justify',
    );

}
