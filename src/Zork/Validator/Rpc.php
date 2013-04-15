<?php

namespace Zork\Validator;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zork\Rpc\CallableInterface;
use Zend\Validator\AbstractValidator;
use Zork\Rpc\Exception\BadMethodCallException;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zork\ServiceManager\ApplicationServiceLocatorAwareInterface;

/**
 * Rpc
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Rpc extends AbstractValidator
       implements ApplicationServiceLocatorAwareInterface
{

    /**
     * @const string
     */
    const MISSING_LOCATOR   = 'missingLocator';

    /**
     * @const string
     */
    const MISSING_SERVICE   = 'missingService';

    /**
     * @const string
     */
    const MISSING_METHOD    = 'missingMethod';

    /**
     * @const string
     */
    const NOT_VALID_SERVICE = 'notValidService';

    /**
     * @const string
     */
    const NOT_VALID_METHOD  = 'notValidMethod';

    /**
     * @const string
     */
    const RETURN_EMPTY      = 'returnEmpty';

    /**
     * @const string
     */
    const RETURN_FALSE      = 'returnFalse';

    /**
     * @const string
     */
    const RETURN_NULL       = 'returnNull';

    /**
     * @const string
     */
    const EXCEPTION         = 'exception';

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::RETURN_FALSE      => 'validate.rpc.returnFalse',
        self::RETURN_NULL       => 'validate.rpc.returnNull',
        self::RETURN_EMPTY      => 'validate.rpc.returnEmpty',
        self::NOT_VALID_SERVICE => 'validate.rpc.notValidService',
        self::NOT_VALID_METHOD  => 'validate.rpc.notValidMethod',
        self::MISSING_LOCATOR   => 'validate.rpc.missingLocator',
        self::MISSING_SERVICE   => 'validate.rpc.missingService',
        self::MISSING_METHOD    => 'validate.rpc.missingMethod',
        self::EXCEPTION         => 'validate.rpc.exception',
    );

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var string
     */
    protected $service;

    /**
     * @var string
     */
    protected $method = '__invoke';

    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zork\Validator\Rpc
     */
    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param string $service
     * @return \Zork\Validator\Rpc
     */
    public function setService( $service )
    {
        $this->service = (string) $service;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return \Zork\Validator\Rpc
     */
    public function setMethod( $method )
    {
        $this->method = (string) $method;
        return $this;
    }

    /**
     * Sets validator options
     *
     * @param  mixed $rpc
     */
    public function __construct( $rpc = null )
    {
        if ( $rpc instanceof Traversable )
        {
            $rpc = ArrayUtils::iteratorToArray( $rpc );
        }

        if ( is_array( $rpc ) && array_key_exists( 'service', $rpc ) )
        {
            if ( array_key_exists( 'method', $rpc ) )
            {
                $this->setMethod( $rpc['method'] );
            }

            $this->setService( $rpc['service'] );
        }
        elseif ( null !== $rpc )
        {
            $this->setService( $rpc );
        }

        parent::__construct( is_array( $rpc ) ? $rpc : null );
    }

    /**
     * Returns true if and only if the provided rpc returns true
     * for value / context
     *
     * @param  mixed $value
     * @param  array $context
     * @return boolean
     */
    public function isValid( $value, $context = null )
    {
        $serviceLocator = $this->getServiceLocator();
        $serviceName    = $this->getService();
        $method         = $this->getMethod();

        if ( ! $serviceLocator instanceof ServiceLocatorInterface )
        {
            $this->error( self::MISSING_LOCATOR );
            return false;
        }

        if ( empty( $serviceName ) )
        {
            $this->error( self::MISSING_SERVICE );
            return false;
        }

        if ( empty( $method ) )
        {
            $this->error( self::MISSING_METHOD );
            return false;
        }

        $service = $serviceLocator->get( $serviceName );

        if ( ! $service instanceof CallableInterface )
        {
            $this->error( self::NOT_VALID_SERVICE );
            return false;
        }

        try
        {
            $result = $service->call( $method, array( $value, $context ) );
        }
        catch ( BadMethodCallException $ex )
        {
            $this->error( self::NOT_VALID_METHOD, $ex->getMessage() );
            return false;
        }
        catch ( \Exception $ex )
        {
            $this->error( self::EXCEPTION, $ex->getMessage() );
            return false;
        }

        if ( is_array( $result ) )
        {
            if ( empty( $result ) )
            {
                $this->error( self::RETURN_EMPTY );
                return false;
            }

            $success = array_shift( $result );

            if ( empty( $result ) )
            {
                $message = null;
            }
            else
            {
                $message = array_shift( $result );
            }
        }
        else if ( is_object( $result ) )
        {
            if ( isset( $result->success ) )
            {
                $success = $result->success;
            }
            else
            {
                $success = null;
            }

            if ( isset( $result->message ) )
            {
                $message = $result->message;
            }
            else
            {
                $message = null;
            }
        }
        else if ( is_string( $result ) )
        {
            if ( empty( $result ) )
            {
                $success = true;
                $message = null;
            }
            else
            {
                $success = false;
                $message = $result;
            }
        }
        else
        {
            $success = $result;
            $message = null;
        }

        if ( $result === null )
        {
            $this->error( self::RETURN_NULL, $message );
            return false;
        }

        if ( $result === false )
        {
            $this->error( self::RETURN_FALSE, $message );
            return false;
        }

        if ( empty( $result ) )
        {
            $this->error( self::RETURN_EMPTY, $message );
            return false;
        }

        return true;
    }

}
