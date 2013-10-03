<?php

namespace Zork\Mvc\View\Http;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * ForbiddenStrategy
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ForbiddenStrategy implements ListenerAggregateInterface
{

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Template to use to report page forbidden conditions
     *
     * @var string
     */
    protected $forbiddenTemplate = 'error';

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach( EventManagerInterface $events )
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH,
            array( $this, 'prepareForbiddenViewModel' ),
            -85
        );
    }

    /**
     * Detach aggregate listeners from the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach( EventManagerInterface $events )
    {
        foreach ( $this->listeners as $index => $listener )
        {
            if ( $events->detach( $listener ) )
            {
                unset( $this->listeners[$index] );
            }
        }
    }

    /**
     * Get template for forbidden conditions
     *
     * @param  string $forbiddenTemplate
     * @return \Zork\Mvc\View\Http\ForbiddenStrategy
     */
    public function setForbiddenTemplate( $forbiddenTemplate )
    {
        $this->forbiddenTemplate = (string) $forbiddenTemplate;
        return $this;
    }

    /**
     * Get template for forbidden conditions
     *
     * @return string
     */
    public function getForbiddenTemplate()
    {
        return $this->forbiddenTemplate;
    }

    /**
     * Create and return a 403 view model
     *
     * @param  MvcEvent $event
     * @return void
     */
    public function prepareForbiddenViewModel( MvcEvent $event )
    {
        if ($event->getRequest() instanceof \Zend\Console\Request) {
            // CLI mode
            return;
        }
        
        $vars = $event->getResult();
        if ( $vars instanceof Response )
        {
            // Already have a response as the result
            return;
        }

        $response = $event->getResponse();
        if ( $response->getStatusCode() != 403 )
        {
            // Only handle 403 responses
            return;
        }

        if ( ! $vars instanceof ViewModel )
        {
            $model = new ViewModel();
            if ( is_string( $vars ) )
            {
                $model->setVariable( 'message', $vars );
            }
            else
            {
                $model->setVariable( 'message', 'Page is forbidden.' );
            }
        }
        else
        {
            $model = $vars;
            if ( $model->getVariable( 'message' ) === null )
            {
                $model->setVariable( 'message', 'Page is forbidden.' );
            }
        }

        $model->setTemplate( $this->getForbiddenTemplate() );
        $event->setResult( $model );
    }

}
