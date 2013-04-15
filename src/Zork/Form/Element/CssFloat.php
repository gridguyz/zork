<?php

namespace Zork\Form\Element;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CssFloat extends AbstractSelectDefaultOptions
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
        ''      => 'css.default',
        'none'  => 'css.float.none',
        'left'  => 'css.float.left',
        'right' => 'css.float.right',
    );

}
