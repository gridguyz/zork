<?php

namespace Zork\Form\Element;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CssRepeat extends AbstractSelectDefaultOptions
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
        'repeat'    => 'css.background-repeat.repeat',
        'repeat-x'  => 'css.background-repeat.repeat-x',
        'repeat-y'  => 'css.background-repeat.repeat-y',
        'no-repeat' => 'css.background-repeat.no-repeat',
    );

}
