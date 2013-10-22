<?php

namespace Zork\Log\Formatter;

use Zend\Debug\Debug;
use Zend\Log\Formatter\Simple;

/**
 * ExtraHandler
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ExtraHandler extends Simple
{

    /**
     * @const string
     */
    const DEFAULT_FORMAT = "%timestamp% %priorityName% (%priority%)\n\n%message%\n\n%extra%";

    /**
     * This method formats the event for the PHP Exception
     *
     * @param   array   $event
     * @return  string
     */
    public function format( $event )
    {
        $output = $this->format;

        foreach ( $event as $key => $value )
        {
            if ( 'extra' === $key )
            {
                $value = Debug::dump( $value, $key, false );
            }
            else
            {
                $value = $this->normalize( $value );
            }

            $output = str_replace( "%$key%", $value, $output );
        }

        if ( isset( $event['extra'] ) && empty( $event['extra'] ) &&
             false !== strpos( $this->format, '%extra%' ) )
        {
            $output = rtrim( $output, ' ' );
        }

        return $output;
    }

}
