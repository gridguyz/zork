<?php

namespace Zork\Log;

use InvalidArgumentException;
use Zend\Log\Logger;
use Zend\Log\Writer\WriterInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoggerManager
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param array|\Zend\ServiceManager\ServiceLocatorInterface $config
     * @return \Zork\Log\LoggerManager
     */
    public static function factory( $config = null )
    {
        return new static( $config );
    }

    /**
     * @param array|\Zend\ServiceManager\ServiceLocatorInterface $config
     */
    public function __construct( $config = null )
    {
        if ( is_array( $config ) )
        {
            $this->setConfig( $config );
        }
        elseif ( $config instanceof ServiceLocatorInterface )
        {
            $this->setServiceLocator( $config );
            $config = $config->get('Configuration');

            if ( isset( $config['log'] ) )
            {
                $this->setConfig( (array) $config['log'] );
            }
        }
        else if( $config != null )
        {
            throw new InvalidArgumentException(
                'The config must be null, array or ' .
                'instanceof \Zend\ServiceManager\ServiceLocatorInterface'
            );
        }
    }

    /**
     * @param array $config
     * @return \Zork\Log\LoggerManager
     */
    public function setConfig( array $config )
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zork\Log\LoggerManager
     */
    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * @param string $type
     * @throws \InvalidArgumentException
     * @return \Zend\Log\Logger
     */
    public function getLogger( $type )
    {
        $type = (string) $type;

        if ( ! isset( $this->config[$type] ) )
        {
            throw new InvalidArgumentException(
                'Type (' . $type . ') not registered in config'
            );
        }

        $logger = new Logger();

        if ( $this->serviceLocator instanceof ServiceManager )
        {
            $logger->getWriterPluginManager()
                   ->addPeeringServiceManager( $this->serviceLocator );
        }

        if ( isset( $this->config[$type]['writers'] ) )
        {
            foreach ( $this->config[$type]['writers'] as $name => $config )
            {
                $name       = isset( $config['name'] )      ? (string)  $config['name']     : $name;
                $options    = isset( $config['options'] )   ? (array)   $config['options']  : null;
                $priority   = isset( $config['priority'] )  ? (int)     $config['priority'] : null;

                if ( strtolower( $name ) == 'mail' && $this->serviceLocator !== null )
                {
                    if ( ! isset( $options['transport'] ) )
                    {
                        $options['transport'] = $this->serviceLocator
                                                     ->get( 'Zork\Mail\Service' )
                                                     ->getTransport();
                    }

                    if ( isset( $options['mail'] ) && is_array( $options['mail'] ) )
                    {
                        $options['mail'] = $this->serviceLocator
                                                ->get( 'Zork\Mail\Service' )
                                                ->createMessage( $options['mail'] );
                    }
                }

                if ( is_string( $name ) )
                {
                    $writer = $logger->writerPlugin( $name, $options );
                }
                else
                {
                    $writer = $name;
                }

                if ( $writer instanceof WriterInterface )
                {
                    if ( isset( $config['filters'] ) )
                    {
                        foreach ( $config['filters'] as $filterName => $filterConfig )
                        {
                            $filterName     = isset( $filterConfig['name'] )
                                            ? (string) $filterConfig['name']
                                            : $filterName;
                            $filterOptions  = isset( $filterConfig['options'] )
                                            ? (array) $filterConfig['options']
                                            : null;

                            if ( is_string( $filterName ) )
                            {
                                $filter = $writer->filterPlugin(
                                    $filterName,
                                    $filterOptions
                                );
                            }
                            else
                            {
                                $filter = $filterName;
                            }

                            $writer->addFilter( $filter, $filterOptions );
                        }
                    }

                    if ( isset( $config['formatter'] ) )
                    {
                        if ( isset( $config['formatter']['name'] ) )
                        {
                            $formatter = $writer->formatterPlugin(
                                $config['formatter']['name'],
                                isset( $config['formatter']['options'] )
                                    ? $config['formatter']['options']
                                    : array()
                            );
                        }
                        else
                        {
                            $formatter = $config['formatter'];
                        }

                        $writer->setFormatter( $formatter );
                    }
                }

                $logger->addWriter( $writer, $priority, $options );
            }
        }

        if ( isset( $this->config[$type]['processors'] ) )
        {
            foreach ( $this->config[$type]['processors'] as $name => $config )
            {
                $name       = isset( $config['name'] )      ? (string)  $config['name']     : $name;
                $options    = isset( $config['options'] )   ? (array)   $config['options']  : null;
                $priority   = isset( $config['priority'] )  ? (int)     $config['priority'] : null;

                $logger->addProcessor( $name, $priority, $options );
            }
        }

        return $logger;
    }

    /**
     * @param string $type
     * @return boolean
     */
    public function hasLogger( $type )
    {
        return isset( $this->config[$type] );
    }

}
