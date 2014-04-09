<?php

namespace Zork\Form\Element;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CssFontWeight extends AbstractSelectDefaultOptions
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
        'normal'    => 'css.font-weight.normal',
        'bold'      => 'css.font-weight.bold',
        'lighter'   => 'css.font-weight.lighter',
        'bolder'    => 'css.font-weight.bolder',
        '100'       => 'css.font-weight.100',
        '200'       => 'css.font-weight.200',
        '300'       => 'css.font-weight.300',
        '400'       => 'css.font-weight.400',
        '500'       => 'css.font-weight.500',
        '600'       => 'css.font-weight.600',
        '700'       => 'css.font-weight.700',
        '800'       => 'css.font-weight.800',
        '900'       => 'css.font-weight.900',
    );

}
