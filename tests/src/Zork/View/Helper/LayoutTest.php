<?php

namespace Zork\View\Helper;

use Zend\View\Model\ViewModel;
use Zork\Test\PHPUnit\View\Helper\TestCase;

/**
 * LayoutTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\View\Helper\Layout
 */
class LayoutTest extends TestCase
{

    /**
     * @var string
     */
    protected static $rendererClass = 'Zend\View\Renderer\PhpRenderer';

    /**
     * @var string
     */
    protected static $helperClass = 'Zork\View\Helper\Layout';

    /**
     * Test locales
     */
    public function testMiddleLayout()
    {
        $vars = array(
            'variable1' => 'value1',
            'variable2' => 'value2',
        );

        $root = new ViewModel( array(
            'root1' => 'value1',
            'root2' => 'value2',
        ) );

        $viewModelHelper = $this->plugin( 'view_model' );
        $this->pluginInstances['view_model'] = $viewModelHelper->setRoot( $root );

        $this->helper->setMiddleLayout( 'layout-name', $vars );

        /* @var $middleLayout \Zork\Mvc\Controller\Plugin\MiddleLayoutInterface */
        $middleLayout = $this->helper->getMiddleLayout();

        $this->assertInstanceOf( 'Zork\Mvc\Controller\Plugin\MiddleLayoutInterface', $middleLayout );
        $this->assertEquals( 'layout-name', $middleLayout->getTemplate() );
        $this->assertEquals( $vars, $middleLayout->getVariables() );
    }

}
