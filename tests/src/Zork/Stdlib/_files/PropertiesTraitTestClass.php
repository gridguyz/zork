<?php

namespace Zork\Stdlib;

/**
 * Zork\Stdlib\PropertiesTraitTestClass
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class PropertiesTraitTestClass
{

    use PropertiesTrait;

    public $publicProperty;

    public $_hiddenPublicProperty;

    protected $protectedProperty;

    protected $_hiddenProtectedProperty;

    private $privateProperty;

    private $_hiddenPrivateProperty;

    public function setPublicPropertyWithSetter( $value )
    {
        $this->publicProperty = $value;
    }

    public function setProtectedPropertyWithSetter( $value )
    {
        $this->protectedProperty = $value;
    }

    public function setPrivatePropertyWithSetter( $value )
    {
        $this->privateProperty = $value;
    }

    public function getPublicPropertyWithGetter()
    {
        return $this->publicProperty;
    }

    public function getProtectedPropertyWithGetter()
    {
        return $this->protectedProperty;
    }

    public function getPrivatePropertyWithGetter()
    {
        return $this->privateProperty;
    }

    public function issetPublicPropertyWithIssetter()
    {
        return isset( $this->publicProperty );
    }

    public function issetProtectedPropertyWithIssetter()
    {
        return isset( $this->protectedProperty );
    }

    public function issetPrivatePropertyWithIssetter()
    {
        return isset( $this->privateProperty );
    }

    public function unsetPublicPropertyWithUnsetter()
    {
        $this->publicProperty = null;
    }

    public function unsetProtectedPropertyWithUnsetter()
    {
        $this->protectedProperty = null;
    }

    public function unsetPrivatePropertyWithUnsetter()
    {
        $this->privateProperty = null;
    }

}
