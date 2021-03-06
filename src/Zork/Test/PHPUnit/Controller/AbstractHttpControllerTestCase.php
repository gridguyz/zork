<?php

namespace Zork\Test\PHPUnit\Controller;

use Zend\Stdlib\ArrayUtils;
use Zork\Test\PHPUnit\TestCaseTrait;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase as ZendAbstractHttpControllerTestCase;

/**
 * AbstractHttpControllerTestCase
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AbstractHttpControllerTestCase extends ZendAbstractHttpControllerTestCase
{

    use TestCaseTrait;

    /**
     * Application config path
     *
     * @var string
     */
    protected $applicationConfigPath = 'config/application.php';

    
    protected $applicationRootPath = '.';
    
    /**
     * Application config override
     *
     * @var array
     */
    protected $applicationConfigOverride = array();

    /**
     * @var string
     */
    private $originalDbName;

    /**
     * @var string
     */
    private $temporaryDbName;

    
    private $orginalCwd = null;
    
    /**
     * Gc
     */
    private function gc()
    {
        if ( gc_enabled() )
        {
            gc_collect_cycles();
        }
        else
        {
            gc_enable();
            gc_collect_cycles();
            gc_disable();
        }
    }

    public function setApplicationConfigPath($applicationConfigPath) {
        $this->applicationConfigPath = $applicationConfigPath;
        
        return $this;
    }
    
    public function setApplicationRootPath($applicationRootPath) {
        $this->applicationRootPath = $applicationRootPath;
        
        return $this;
    }
    
    /**
     * Set config, clone the db & reset the application for isolation
     */
    public function setUp()
    {
        $this->orginalCwd = getcwd();
        
        chdir($this->applicationRootPath);
        
        parent::setUp();
        $this->gc();
        $config = include $this->applicationConfigPath;

        if ( ! empty( $config['db']['dbname'] ) )
        {
            $this->originalDbName   = $config['db']['dbname'];
            $this->temporaryDbName  = $this->originalDbName . '_unittest_'
                                    . date( 'YmdHis' )
                                    . (int) ( microtime() * 1000 );

            $db         = new DbAdapter( $config['db'] );
            $platform   = $db->getPlatform();

            $db->query(
                sprintf(
                    'CREATE DATABASE %s WITH TEMPLATE %s',
                    $platform->quoteIdentifier( $this->temporaryDbName ),
                    $platform->quoteIdentifier( $this->originalDbName )
                ),
                DbAdapter::QUERY_MODE_EXECUTE
            );

            $config['db']['dbname'] = $this->temporaryDbName;
            $platform = null;
            $db = null;
            $this->gc();
        }

        $this->setApplicationConfig( ArrayUtils::merge(
            $config,
            $this->applicationConfigOverride
        ) );
        
        
    }

    /**
     * Drop temporary db & restore params
     */
    public function tearDown()
    {
        parent::tearDown();

        $sm = $this->getApplication()
                   ->getServiceManager();
        \Zork\ServiceManager\ServiceManager::unregisterServices( $sm );
        $sm = null;

        $config = $this->getApplicationConfig();
        $this->reset();
        $this->gc();

        if ( ! empty( $config['db'] ) && $this->originalDbName )
        {
            $config['db']['dbname'] = $this->originalDbName;
            $db                     = new DbAdapter( $config['db'] );
            $platform               = $db->getPlatform();

            $db->query(
                sprintf(
                    'DROP DATABASE %s',
                    $platform->quoteIdentifier( $this->temporaryDbName )
                ),
                DbAdapter::QUERY_MODE_EXECUTE
            );

            $platform = null;
            $db = null;
            $this->gc();
        }
        
        chdir($this->orginalCwd);
    }

    /**
     * Get a service by its name
     *
     * @param   string  $name
     * @return  mixed
     */
    public function getService( $name )
    {
        return $this->getApplication()
                    ->getServiceManager()
                    ->get( $name );
    }

}
