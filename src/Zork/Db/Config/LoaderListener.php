<?php

namespace Zork\Db\Config;

use Zend\Stdlib\ArrayUtils;
use Zend\Db\Adapter\Adapter;
use Zend\ModuleManager\ModuleEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * LoaderListener
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class LoaderListener implements ListenerAggregateInterface
{

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $dbAdapter;

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * Constructor
     *
     * @param \Zend\Db\Adapter\Adapter $dbAdapter
     */
    public function __construct( Adapter $dbAdapter )
    {
        $this->dbAdapter = $dbAdapter;
    }

    /**
     * Load config from db-adapter
     *
     * @param \Zend\Db\Adapter\Adapter $db
     * @return array
     */
    protected function loadConfig()
    {
        if ( empty( $this->dbAdapter ) )
        {
            return $this->config;
        }

        $platform = $this->dbAdapter->getPlatform();
        $driver   = $this->dbAdapter->getDriver();

        $query = $this->dbAdapter->query( '
            SELECT ' . $platform->quoteIdentifier( 'value' ) . '
              FROM ' . $platform->quoteIdentifier( 'settings' ) . '
             WHERE ' . $platform->quoteIdentifier( 'key' ) . '
                 = ' . $platform->quoteValue( 'ini-cache' ) . '
               AND ' . $platform->quoteIdentifier( 'type' ) . '
                 = ' . $platform->quoteValue( 'ini-cache' ) . '
             LIMIT 1
        ' );

        $this->config   = array();
        $result         = $query->execute();

        if ( $result->getAffectedRows() > 0 )
        {
            foreach ( $result as $cache )
            {
                $this->config = ArrayUtils::merge(
                    $this->config,
                    (array) unserialize( $cache['value'] )
                );
            }
        }
        else
        {
            $query = $this->dbAdapter->query( '
                SELECT ' . $platform->quoteIdentifier( 'key' ) . ',
                       ' . $platform->quoteIdentifier( 'value' ) . '
                  FROM ' . $platform->quoteIdentifier( 'settings' ) . '
                 WHERE ' . $platform->quoteIdentifier( 'type' ) . '
                     = ' . $platform->quoteValue( 'ini' ) . '
            ' );

            foreach ( $query->execute() as $pair )
            {
                $key    = (string) $pair['key'];
                $value  = (string) $pair['value'];
                $entry  = array();
                $curr   = & $entry;

                foreach ( explode( '.', $key ) as $sub )
                {
                    $curr[$sub] = null;
                    $curr       = & $curr[$sub];
                }

                $curr           = $value;
                $this->config   = ArrayUtils::merge( $this->config, $entry );
            }

            $query = $this->dbAdapter->query( '
                INSERT INTO ' . $platform->quoteIdentifier( 'settings' ) . '
                            ( ' . $platform->quoteIdentifier( 'key' ) . ',
                              ' . $platform->quoteIdentifier( 'value' ) . ',
                              ' . $platform->quoteIdentifier( 'type' ) . ' )
                     VALUES ( ' . $driver->formatParameterName( 'key' ) . ',
                              ' . $driver->formatParameterName( 'value' ) . ',
                              ' . $driver->formatParameterName( 'type' ) . ' )
            ' );

            $query->execute( array(
                'key'   => 'ini-cache',
                'value' => serialize( $this->config ),
                'type'  => 'ini-cache',
            ) );
        }

        $this->dbAdapter = null;
        return $this->config;
    }

    /**
     * On all modules loaded
     *
     * @param \Zend\ModuleManager\ModuleEvent $event
     */
    public function onModulesLoaded( ModuleEvent $event )
    {
        $listener = $event->getConfigListener();
        $fakeLoad = clone $event;

        $listener->onLoadModule(
            $fakeLoad->setModuleName( '' )
                     ->setModule( new DbModule( $this->loadConfig() ) )
        );
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     */
    public function attach( EventManagerInterface $events, $priority = -999 )
    {
        $this->listeners[] = $events->attach(
            ModuleEvent::EVENT_LOAD_MODULES,
            array( $this, 'onModulesLoaded' ),
            (int) $priority
        );

        return $this;
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     */
    public function detach( EventManagerInterface $events )
    {
        foreach ( $this->listeners as $listener )
        {
            $events->detach( $listener );
        }

        $this->listeners = array();
        return $this;
    }

}
