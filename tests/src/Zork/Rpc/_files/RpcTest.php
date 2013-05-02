<?php

namespace Zork\Rpc;

use stdClass;

/**
 * Rpc
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class RpcTest implements CallableInterface
{

    use CallableTrait;

    /**
     * @var array
     */
    static $notCallableMethods = array(
        'notCallable',
    );

    /**
     * @var array
     */
    static $onlyCallableMethods = array(
        'defaultValue',
        'allowsNull',
        'required',
        'notExists',
    );

    /**
     * @param   bool    $bool
     * @param   int     $int
     * @param   float   $float
     * @param   string  $string
     * @param   array   $array
     * @return  array
     */
    public function defaultValue( $bool     = false,
                                  $int      = 0,
                                  $float    = 0.0,
                                  $string   = '0',
                                  $array    = array() )
    {
        return array(
            'bool'      => $bool,
            'int'       => $int,
            'float'     => $float,
            'string'    => $string,
            'array'     => $array,
        );
    }

    /**
     * @param   array       $array
     * @param   stdClass    $stdClass
     * @return  array
     */
    public function allowsNull( array    $array     = null,
                                stdClass $stdClass  = null )
    {
        return array(
            'array'     => $array,
            'stdClass'  => $stdClass,
        );
    }

    /**
     * @param   mixed   $required
     * @return  mixed
     */
    public function required( $required )
    {
        return $required;
    }

}
