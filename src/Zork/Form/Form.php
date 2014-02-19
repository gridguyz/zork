<?php

namespace Zork\Form;

use Zend\Json\Json;
use Zend\Form\Form as Base;
use Zend\Form\FormInterface;
/**
 * Zork Form
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @author Mihaly Farkas <mihaly.farkas@megaweb.hu>
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
     * @const string
     */
    const BIND_PLUGIN_CLASSES = 'bind-plugin-classes';
    
    /**
     * @const string
     */
    const SET_DATA_PLUGIN_CLASSES = 'set-data-plugin-classes';

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

    public function setData($data) {
        
        parent::setData($data);
        
        $pluginClasses = $this->getAttribute( static::SET_DATA_PLUGIN_CLASSES );
        
        if ( !empty( $pluginClasses ) )
        {
            if (!is_array($pluginClasses)) {
                throw new \InvalidArgumentException('The plugin classes attribute must be an array');
            }
        
            foreach ($pluginClasses as $pluginClass) {
        
                if (!is_subclass_of($pluginClass, '\Zork\Form\Plugin\SetDataInterface')) {
                    throw new \InvalidArgumentException('The plugin class must be implement \Zork\Form\Plugin\SetDataInterface');
                }
        
                $plugin = new $pluginClass();
                
                $plugin->setData($this, $data);
        
            }
        }
        
        return $this;
    }
    
    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        parent::bind($object, $flags);
        
        $pluginClasses = $this->getAttribute( static::BIND_PLUGIN_CLASSES );
        
        if ( !empty( $pluginClasses ) )
        {
            if (!is_array($pluginClasses)) {
                throw new \InvalidArgumentException('The plugin classes attribute must be an array');
            }
            
            foreach ($pluginClasses as $pluginClass) {
                
                if (!is_subclass_of($pluginClass, '\Zork\Form\Plugin\BindInterface')) {
                    throw new \InvalidArgumentException('The plugin class must be implement \Zork\Form\Plugin\BindInterface');
                }
                
                $plugin = new $pluginClass();
                
                $plugin->bind($this);
                
            }
        }
        
        return $this;
    }
    
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
