<?php

namespace Zork\Form;

use Zend\Json\Json;
use Zend\Form\Form as Base;

/**
 * Zork Form
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Form extends Base
{

    /**
     * @const string
     */
    const ATTR_JS_TYPE = 'data-js-type';

    /**
     * @const string
     */
    const JS_TYPE_CANCEL = 'js.form.cancel';

    /**
     * @const string
     */
    const JS_CANCEL_ATTR = 'data-js-cancel-buttons';

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'method'    => 'POST',
        'class'     => 'zork-form',
    );

    /**
     * @var bool
     */
    private $cancelAdded = false;

    /**
     * Get js-types
     *
     * @return array
     */
    public function getJsTypes()
    {
        $types = $this->getAttribute( static::ATTR_JS_TYPE );

        if ( empty( $types ) )
        {
            return array();
        }

        return explode( ' ', $types );
    }

    /**
     * Set js-types
     *
     * @param   string|array $types
     * @return  Form
     */
    public function setJsTypes( $types )
    {
        return $this->setAttribute(
            static::ATTR_JS_TYPE,
            is_array( $types ) ? implode( ' ', $types ) : $types
        );
    }

    /**
     * Add js-types
     *
     * @param   string|array $types
     * @return  Form
     */
    public function addJsTypes( $types )
    {
        $before = $this->getAttribute( static::ATTR_JS_TYPE );

        if ( ! empty( $before ) )
        {
            $before .= ' ';
        }

        return $this->setAttribute(
            static::ATTR_JS_TYPE,
            $before . ( is_array( $types ) ? implode( ' ', $types ) : $types )
        );
    }

    /**
     * Set cancel buttons
     *
     * @param   string|array $urlMap
     * @return  Form
     */
    public function setCancel( $urlMap )
    {
        if ( ! $this->cancelAdded )
        {
            $this->addJsTypes( static::JS_TYPE_CANCEL );
            $this->cancelAdded = true;
        }

        return $this->setAttribute(
            static::JS_CANCEL_ATTR,
            Json::encode(
                is_array( $urlMap ) || is_object( $urlMap )
                    ? $urlMap
                    : array( 'default.cancel' => (string) $urlMap )
            )
        );
    }

}
