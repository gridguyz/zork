<?php
namespace Zork\Test\PHPUnit;

/**
 * TestCaseTrait
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait TestCaseTrait
{

    /**
     * Assert every values to match for a callback, or a constraint
     *
     * @param callable|\PHPUnit_Framework_Constraint $callbackOrConstraint            
     * @param array|\Traversable $arrayOrTraversable            
     * @param string $message            
     */
    public static function assertEvery($callbackOrConstraint, $arrayOrTraversable, $message = '')
    {
        $constraint = new Constraint\Every($callbackOrConstraint);
        self::assertThat($arrayOrTraversable, $constraint, $message);
    }

    /**
     * Assert some values to match for a callback, or a constraint
     *
     * @param callable|\PHPUnit_Framework_Constraint $callbackOrConstraint            
     * @param array|\Traversable $arrayOrTraversable            
     * @param string $message            
     */
    public static function assertSome($callbackOrConstraint, $arrayOrTraversable, $message = '')
    {
        $constraint = new Constraint\Some($callbackOrConstraint);
        self::assertThat($arrayOrTraversable, $constraint, $message);
    }

    /**
     * Check the module loaded after application initialization
     *
     * @param string $moduleName            
     * @param string $message            
     */
    public function assertModuleLoaded($moduleName, $message = '')
    {
        $application = $this->getApplication();
        $serviceManager = $application->getServiceManager();
        
        /* @var $moduleManager \Zend\ModuleManager\ModuleManager */
        $moduleManager = $serviceManager->get('ModuleManager');
        
        if ($message == '') {
            $message = 'Module "' . $moduleName . '" not loaded';
        }
        
        $this->assertThat(is_object($moduleManager->getModule($moduleName)), static::isTrue(), $message);
    }

    public function assertModuleName($module)
    {
        $controllerClass = $this->getControllerFullClassName();
        
        $parts = substr_count($module, '\\') + 1;
        $offset = 0;
        
        while ($parts --) {
            $offset = strpos($controllerClass, '\\', $offset + 1);
        }
        
        $match = substr($controllerClass, 0, $offset);
        $match = strtolower($match);
        $module = strtolower($module);
        
        if ($module != $match) {
            throw new \PHPUnit_Framework_ExpectationFailedException(
                sprintf('Failed asserting module name "%s", actual module name is "%s"', $module, $match));
        }
        $this->assertEquals($module, $match);
    }

    /**
     * Assert that the application route match used the given controller class
     *
     * @param string $controller            
     */
    public function assertControllerClass($controller)
    {
        $controllerClass = $this->getControllerFullClassName();
        
        $match = substr($controllerClass, strrpos($controllerClass, '\\') + 1);
        $match = strtolower($match);
        
        $controller = strtolower($controller);
        
        if ($controller != $match) {
            
            $match = $controllerClass;
            $match = strtolower($match);
            
            if ($controller != $match) {
                
                throw new PHPUnit_Framework_ExpectationFailedException(
                    sprintf('Failed asserting controller class "%s", actual controller class is "%s"', $controller, 
                        $match));
            }
        }
        $this->assertEquals($controller, $match);
    }

    /**
     * Assert every values to match for a callback, or a constraint
     *
     * @param callable|\PHPUnit_Framework_Constraint $callbackOrConstraint            
     * @return Constraint\Every
     */
    public static function every($callbackOrConstraint)
    {
        return new Constraint\Every($callbackOrConstraint);
    }

    /**
     * Assert some values to match for a callback, or a constraint
     *
     * @param callable|\PHPUnit_Framework_Constraint $callbackOrConstraint            
     * @return Constraint\Some
     */
    public static function some($callbackOrConstraint)
    {
        return new Constraint\Some($callbackOrConstraint);
    }

    /**
     * Assert count equals to expected
     *
     * @param int $expectedCount            
     * @return \PHPUnit_Framework_Constraint_Count
     */
    public static function countEquals($expectedCount)
    {
        return new \PHPUnit_Framework_Constraint_Count($expectedCount);
    }
}
