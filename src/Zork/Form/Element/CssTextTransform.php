<?php

namespace Zork\Form\Element;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CssTextTransform extends AbstractSelectDefaultOptions
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
        ''              => 'css.default',
        'none'          => 'css.text-transform.none',
        'capitalize'    => 'css.text-transform.capitalize',
        'uppercase'     => 'css.text-transform.uppercase',
        'lowercase'     => 'css.text-transform.lowercase',
    );

}
