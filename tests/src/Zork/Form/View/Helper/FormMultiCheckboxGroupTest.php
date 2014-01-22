<?php

namespace Zork\Form\View\Helper;

use Zork\Form\Element\Text;
use Zork\Form\Element\MultiCheckboxGroup;
use Zork\Test\PHPUnit\View\Helper\TestCase;

/**
 * FormMultiCheckboxGroupTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormMultiCheckboxGroupTest extends TestCase
{

    /**
     * @var string
     */
    protected static $rendererClass = 'Zend\View\Renderer\PhpRenderer';

    /**
     * @var string
     */
    protected static $helperClass = 'Zork\Form\View\Helper\FormMultiCheckboxGroup';

    /**
     * Test invoke returns self
     */
    public function testInvokeReturnSelf()
    {
        $this->assertInstanceOf( static::$helperClass, $this->helper() );
    }

    /**
     * Test invoke with other element
     */
    public function testInvokeWithOtherElement()
    {
        $this->setExpectedException( 'Zend\Form\Exception\InvalidArgumentException' );
        $this->helper( new Text );
    }

    /**
     * Test invoke without name
     */
    public function testInvokeWithoutName()
    {
        $this->setExpectedException( 'Zend\Form\Exception\DomainException' );
        $this->helper( new MultiCheckboxGroup );
    }

    /**
     * Test invoke with empty options
     */
    public function testInvokeWithEmptyOptions()
    {
        $checkboxes = new MultiCheckboxGroup( 'test_radiogroup' );
        $this->assertEmpty( $this->helper( $checkboxes ) );

        $translator = $this->getMock( 'Zend\I18n\Translator\Translator' );

        $translator->expects( $this->once() )
                   ->method( 'translate' )
                   ->with( 'default.empty', 'default' )
                   ->will( $this->returnValue( 'default.empty at default' ) );

        $this->helper->setTranslator( $translator );

        $this->assertTag(
            array( 'content' => 'default.empty at default' ),
            $this->helper( $checkboxes )
        );
    }

    /**
     * Test invoke & render
     */
    public function testInvokeAndRender()
    {
        $checkboxes = new MultiCheckboxGroup( 'test_multicheckboxgroup', array(
            'empty_option'  => 'empty',
            'options'       => array(
                'value 1'   => 'label 1',
                'label 2'   => array(
                    'label'     => 'label 2',
                    'value'     => 'value 2',
                    'checked'   => true,
                ),
                'label 3'   => array(
                    'label'     => 'label 3',
                    'options'   => array(
                        'value 3.1' => 'label 3.1',
                        'label 3.2' => array(
                            'label'     => 'label 3.2',
                            'value'     => 'value 3.2',
                            'disabled'  => true,
                        ),
                    ),
                    'data-foo'  => 'baz',
                ),
            ),
        ) );

        $translator = $this->getMock( 'Zend\I18n\Translator\Translator' );

        $translator->expects( $this->at( 0 ) )
                   ->method( 'translate' )
                   ->with( 'empty', 'default' )
                   ->will( $this->returnValue( 'empty at default' ) );

        $translator->expects( $this->at( 1 ) )
                   ->method( 'translate' )
                   ->with( 'label 1', 'default' )
                   ->will( $this->returnValue( 'label 1 at default' ) );

        $translator->expects( $this->at( 2 ) )
                   ->method( 'translate' )
                   ->with( 'label 2', 'default' )
                   ->will( $this->returnValue( 'label 2 at default' ) );

        $translator->expects( $this->at( 3 ) )
                   ->method( 'translate' )
                   ->with( 'label 3', 'default' )
                   ->will( $this->returnValue( 'label 3 at default' ) );

        $translator->expects( $this->at( 4 ) )
                   ->method( 'translate' )
                   ->with( 'label 3.1', 'default' )
                   ->will( $this->returnValue( 'label 3.1 at default' ) );

        $translator->expects( $this->at( 5 ) )
                   ->method( 'translate' )
                   ->with( 'label 3.2', 'default' )
                   ->will( $this->returnValue( 'label 3.2 at default' ) );

        $helper = $this->helper;
        $helper->setTranslator( $translator );

        $checkboxes->setAttribute( 'data-foo', 'bar' )
                   ->setValue( array( 'value 1', 'value 2' ) )
                   ->setTranslatorTextDomain( 'default' );

        $rendered = $helper( $checkboxes );

        $this->assertTag(
            array(
                'tag'           => 'input',
                'attributes'    => array(
                    'type'      => 'hidden',
                    'name'      => 'test_multicheckboxgroup',
                    'value'     => '',
                ),
                'parent'    => array(
                    'attributes'    => array(
                        'data-foo'  => 'bar',
                    ),
                ),
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'tag'       => 'label',
                'content'   => 'empty at default',
                'parent'    => array(
                    'attributes'    => array(
                        'data-foo'  => 'bar',
                    ),
                ),
                'child'     => array(
                    'tag'           => 'input',
                    'attributes'    => array(
                        'type'      => 'checkbox',
                        'name'      => 'test_multicheckboxgroup[]',
                        'value'     => '',
                    ),
                ),
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'tag'       => 'label',
                'content'   => 'label 1 at default',
                'parent'    => array(
                    'attributes'    => array(
                        'data-foo'  => 'bar',
                    ),
                ),
                'child'     => array(
                    'tag'           => 'input',
                    'attributes'    => array(
                        'type'      => 'checkbox',
                        'name'      => 'test_multicheckboxgroup[]',
                        'value'     => 'value 1',
                        'checked'   => 'checked',
                    ),
                ),
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'tag'       => 'label',
                'content'   => 'label 2 at default',
                'parent'    => array(
                    'attributes'    => array(
                        'data-foo'  => 'bar',
                    ),
                ),
                'child'     => array(
                    'tag'           => 'input',
                    'attributes'    => array(
                        'type'      => 'checkbox',
                        'name'      => 'test_multicheckboxgroup[]',
                        'value'     => 'value 2',
                        'checked'   => 'checked',
                    ),
                ),
            ),
            $rendered
        );

        $this->assertTag(
            $fieldsetMatcher = array(
                'tag'       => 'fieldset',
                'parent'    => array(
                    'attributes'    => array(
                        'data-foo'  => 'bar',
                    ),
                ),
                'child'     => array(
                    'tag'       => 'legend',
                    'content'   => 'label 3 at default',
                ),
                'attributes'    => array(
                    'data-foo'  => 'baz',
                ),
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'tag'       => 'label',
                'content'   => 'label 3.1 at default',
                'parent'    => $fieldsetMatcher,
                'child'     => array(
                    'tag'           => 'input',
                    'attributes'    => array(
                        'type'      => 'checkbox',
                        'name'      => 'test_multicheckboxgroup[]',
                        'value'     => 'value 3.1',
                    ),
                ),
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'tag'       => 'label',
                'content'   => 'label 3.2 at default',
                'parent'    => $fieldsetMatcher,
                'child'     => array(
                    'tag'           => 'input',
                    'attributes'    => array(
                        'type'      => 'checkbox',
                        'name'      => 'test_multicheckboxgroup[]',
                        'value'     => 'value 3.2',
                    ),
                ),
            ),
            $rendered
        );
    }

}
