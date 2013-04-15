<?php

namespace Zork\Form\Element;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CssUnit extends Text
{

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type'          => 'text',
        'pattern'       => '-?(0|[1-9][0-9]*)(\.[0-9]+)?(px|pt|pc|em|ex|cm|mm|in|rem|%)',
        'data-js-type'  => 'js.form.element.cssUnit',
        'data-js-units' => 'px|pt|pc|em|ex|cm|mm|in|rem|%',
    );

    /**
     * @var bool
     */
    protected $negativeEnabled = true;

    /**
     * @var array
     */
    protected $unitsEnabled = array(
        'px', 'pt', 'pc', 'em', 'ex', 'cm', 'mm', 'in', 'rem', '%',
    );

    /**
     * @return \Zork\Form\Element\CssUnit
     */
    private function updatePattern()
    {
        $units = implode( '|', array_map( 'preg_quote', $this->unitsEnabled ) );
        return $this->setAttributes( array(
            'data-js-units' => $units,
            'pattern'       => ( $this->negativeEnabled ? '-?' : '' ) .
                               '(0|[1-9][0-9]*)(\.[0-9]+)?(' . $units . ')'
        ) );
    }

    /**
     * @return bool
     */
    public function getNegativeEnabled()
    {
        return $this->negativeEnabled;
    }

    /**
     * @param bool $flag
     * @return \Zork\Form\Element\CssUnit
     */
    public function setNegativeEnabled( $flag = true )
    {
        $this->negativeEnabled = (bool) $flag;
        return $this->updatePattern();
    }

    /**
     * @return array
     */
    public function getUnitsEnabled()
    {
        return $this->unitsEnabled;
    }

    /**
     * @param array $units
     * @return \Zork\Form\Element\CssUnit
     */
    public function setUnitsEnabled( $units = null )
    {
        $this->unitsEnabled = empty( $units ) ? array( 'px' ) : (array) $units;
        return $this->updatePattern();
    }

}
