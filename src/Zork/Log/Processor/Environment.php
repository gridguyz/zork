<?php

namespace Zork\Log\Processor;

use Zend\Log\Processor\ProcessorInterface;

/**
 * Environment
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Environment implements ProcessorInterface
{

    /**
     * Processes a log message before it is given to the writers
     *
     * @param  array $event
     * @return array
     */
    public function process( array $event )
    {
        if ( ! isset( $event['extra'] ) )
        {
            $event['extra'] = array();
        }

        $event['extra']['server']  = $_SERVER;
        $event['extra']['request'] = $_REQUEST;

        if ( isset( $_SERVER['HTTP_CONTENT_TYPE'] ) &&
             ( $_SERVER['HTTP_CONTENT_TYPE'] == 'text/json' ||
               $_SERVER['HTTP_CONTENT_TYPE'] == 'application/json' ) )
        {
            $event['extra']['json'] = @ json_decode(
                file_get_contents( 'php://input' ),
                true
            );
        }

        return $event;
    }

}
