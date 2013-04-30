<?php

namespace ZorkTest\Stdlib;

use Zork\Stdlib\OptionsTrait;

/**
 * ZorkTest\Stdlib\OptionsTraitTestClass
 *
 * @author pozs
 */
class OptionsTraitTestClass
{

    use OptionsTrait;

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

}
