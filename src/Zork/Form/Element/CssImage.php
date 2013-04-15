<?php

namespace Zork\Form\Element;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CssImage extends Text
{

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type'          => 'text',
        'pattern'       => '(none|url\("[^"]+"\))',
        'data-js-type'  => 'js.form.element.cssImage',
    );

}
