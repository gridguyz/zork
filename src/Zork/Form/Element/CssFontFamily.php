<?php

namespace Zork\Form\Element;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CssFontFamily extends AbstractSelectDefaultOptions
{

    /**
     * Is the translator enabled
     *
     * @var bool
     */
    protected $translatorEnabled = false;

    /**
     * @var array
     */
    protected $defaultValueOptions = array(
        ''                                                      => '',
        'Georgia, serif'                                        => 'Georgia',
        '"Palatino Linotype", "Book Antiqua", Palatino, serif'  => 'Palatino Linotype',
        '"Times New Roman", Times, serif'                       => 'Times New Roman',
        'Arial, Helvetica, sans-serif'                          => 'Arial',
        '"Arial Black", Gadget, sans-serif'                     => 'Arial Black',
        '"Comic Sans MS", cursive, sans-serif'                  => 'Comic Sans',
        'Impact, Charcoal, sans-serif'                          => 'Impact',
        '"Lucida Sans Unicode", "Lucida Grande", sans-serif'    => 'Lucida',
        'Tahoma, Geneva, sans-serif'                            => 'Tahoma',
        '"Trebuchet MS", Helvetica, sans-serif'                 => 'Trebuchet',
        'Verdana, Geneva, sans-serif'                           => 'Verdana',
        '"Courier New", Courier, monospace'                     => 'Courier New',
        '"Lucida Console", Monaco, monospace'                   => 'Lucida Console',
        'Consolas, Courier, monospace'                          => 'Consolas',
    );

}
