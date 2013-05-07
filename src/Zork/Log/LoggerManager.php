<?php

namespace Zork\Log;

use Zend\Log\Logger;
use Zend\Log\Exception;
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
     * @param   array                                           $config
     * @param   \Zend\ServiceManager\ServiceLocatorInterface    $serviceLocator
     */
    public function __construct( array $config = null,
                                 ServiceLocatorInterface $serviceLocator = null )
    {
        if ( ! empty( $config ) )
        {
            $this->setConfig( $config );
        }

        if ( ! empty( $serviceLocator ) )
        {
            $this->setServiceLocator( $serviceLocator );
        }
    }

    /**
     * @param   array   $config
     * @return  \Zork\Log\LoggerManager
     */
    public function setConfig( array $config )
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param   \Zend\ServiceManager\ServiceLocatorInterface    $serviceLocator
     * @return  \Zork\Log\LoggerManager
     */
    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * @param   string  $type
     * @throws  \InvalidArgumentException
     * @return  \Zend\Log\Logger
     */
    public function getLogger( $type )
    {
        $type = (string) $type;

        if ( ! isset( $this->config[$type] ) )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                'Type "%s" not registered in config',
                $type
            ) );
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

                if ( $this->serviceLocator !== null &&
                     in_array( strtolower( $name ), array( 'mail', 'zend\log\writer\mail' ) ) )
                {
                    if ( ! isset( $options['transport'] ) )
                    {
                        $options['transport'] = $this->serviceLocator
                                                     ->get( 'Zork\Mail\Service' )
                                                     ->getTransport();
                    }

                    if ( ! isset( $options['mail'] ) )
                    {
                        $options['mail'] = array();
                    }

                    $options['mail'] = $this->serviceLocator
                                            ->get( 'Zork\Mail\Service' )
                                            ->createMessage( $options['mail'] );
                }

                $writer = $logger->writerPlugin( $name, $options );

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

                            $filter = $writer->filterPlugin(
                                $filterName,
                                $filterOptions
                            );

                            $writer->addFilter( $filter, $filterOptions );
                        }
                    }

                    if ( isset( $config['formatter']['name'] ) )
                    {
                        $formatter = $writer->formatterPlugin(
                            $config['formatter']['name'],
                            isset( $config['formatter']['options'] )
                                ? $config['formatter']['options']
                                : array()
                        );

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
     * @param   string  $type
     * @return  boolean
     */
    public function hasLogger( $type )
    {
        return isset( $this->config[$type] );
    }

}
