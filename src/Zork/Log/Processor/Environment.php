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
     * @param   array   $event
     * @return  array
     */
    public function process( array $event )
    {
        if ( ! isset( $event['extra'] ) )
        {
            $event['extra'] = array();
        }

        $event['extra']['server']  = $_SERVER;
        $event['extra']['request'] = $_REQUEST;

        // @codeCoverageIgnoreStart

        switch ( true )
        {
            case isset( $_SERVER['CONTENT_TYPE'] ):
                $contentType = $_SERVER['CONTENT_TYPE'];
                break;

            case isset( $_SERVER['HTTP_CONTENT_TYPE'] ):
                $contentType = $_SERVER['HTTP_CONTENT_TYPE'];
                break;

            case function_exists( 'getallheaders' ):
                $contentType = '';

                foreach ( getallheaders() as $field => $value )
                {
                    if ( strtolower( $field ) == 'content-type' )
                    {
                        $contentType = $value;
                        break;
                    }
                }

            default:
                $contentType = '';
                break;
        }

        $contentType = preg_replace( '/;.*$/', '', $contentType );

        if ( preg_match( '/[\+\/]json$/', $contentType ) )
        {
            $event['extra']['json'] = @ json_decode(
                file_get_contents( 'php://input' ),
                true
            );
        }

        if ( preg_match( '/[\+\/]xml$/', $contentType ) )
        {
            $event['extra']['xml'] = @ file_get_contents( 'php://input' );
        }

        // @codeCoverageIgnoreEnd

        return $event;
    }

}
