<?php

namespace Zork\Form\View\Helper;

use Zork\Form\Element\Text;
use Zork\Form\Element\RadioGroup;
use Zork\Test\PHPUnit\View\Helper\TestCase;

/**
 * FormRadioGroupTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormRadioGroupTest extends TestCase
{

    /**
     * @var string
     */
    protected static $rendererClass = 'Zend\View\Renderer\PhpRenderer';

    /**
     * @var string
     */
    protected static $helperClass = 'Zork\Form\View\Helper\FormRadioGroup';

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
        $this->helper( new RadioGroup );
    }

    /**
     * Test invoke with empty options
     */
    public function testInvokeWithEmptyOptions()
    {
        $radios = new RadioGroup( 'test_radiogroup' );
        $this->assertEmpty( $this->helper( $radios ) );

        $translator = $this->getMock( 'Zend\I18n\Translator\Translator' );

        $translator->expects( $this->once() )
                   ->method( 'translate' )
                   ->with( 'default.empty', 'default' )
                   ->will( $this->returnValue( 'default.empty at default' ) );

        $this->helper->setTranslator( $translator );

        $this->assertTag(
            array( 'content' => 'default.empty at default' ),
            $this->helper( $radios )
        );
    }

    /**
     * Test invoke & render
     */
    public function testInvokeAndRender()
    {
        $radios = new RadioGroup( 'test_radiogroup', array(
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
        $radios->setAttribute( 'data-foo', 'bar' );
        $rendered = $helper( $radios );

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
                        'type'      => 'radio',
                        'name'      => 'test_radiogroup',
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
                        'type'      => 'radio',
                        'name'      => 'test_radiogroup',
                        'value'     => 'value 1',
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
                        'type'      => 'radio',
                        'name'      => 'test_radiogroup',
                        'value'     => 'value 2',
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
                        'type'      => 'radio',
                        'name'      => 'test_radiogroup',
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
                        'type'      => 'radio',
                        'name'      => 'test_radiogroup',
                        'value'     => 'value 3.2',
                    ),
                ),
            ),
            $rendered
        );
    }

    /**
     * Test attribute filters
     */
    public function testAttributeFilters()
    {
        $radios = new RadioGroup( 'test radiogroup', array(
            'empty_option'  => 'empty',
            'options'       => array(
                'value 1'   => 'label 1',
                'label 2'   => array(
                    'label'     => 'label 2',
                    'value'     => 'value 2',
                    'checked'   => true,
                ),
            ),
            'option_attribute_filters' => array(
                'name' => 'escapeUrl',
            ),
        ) );

        $rendered = $this->helper( $radios );

        $this->assertTag(
            array(
                'tag'       => 'label',
                'content'   => 'empty',
                'child'     => array(
                    'tag'           => 'input',
                    'attributes'    => array(
                        'type'      => 'radio',
                        'name'      => 'test%20radiogroup',
                        'value'     => '',
                    ),
                ),
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'tag'       => 'label',
                'content'   => 'label 1',
                'child'     => array(
                    'tag'           => 'input',
                    'attributes'    => array(
                        'type'      => 'radio',
                        'name'      => 'test%20radiogroup',
                        'value'     => 'value 1',
                    ),
                ),
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'tag'       => 'label',
                'content'   => 'label 2',
                'child'     => array(
                    'tag'           => 'input',
                    'attributes'    => array(
                        'type'      => 'radio',
                        'name'      => 'test%20radiogroup',
                        'value'     => 'value 2',
                    ),
                ),
            ),
            $rendered
        );
    }

}
