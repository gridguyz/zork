<?php

namespace Zork\Log\Writer;

use Zend\Log\Writer\AbstractWriter;

/**
 * FormattedMock
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormattedMock extends AbstractWriter
{

    /**
     * array of log messages
     *
     * @var array
     */
    public $messages = array();

    /**
     * shutdown called?
     *
     * @var bool
     */
    public $shutdown = false;

    /**
     * Write a message to the log.
     *
     * @param   array   $event event data
     * @return  void
     */
    public function doWrite( array $event )
    {
        $this->messages[] = $this->formatter->format( $event );
    }

    /**
     * Record shutdown
     *
     * @return  void
     */
    public function shutdown()
    {
        $this->shutdown = true;
    }

}
