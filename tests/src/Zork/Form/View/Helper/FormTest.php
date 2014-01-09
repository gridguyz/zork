<?php

namespace Zork\Form\View\Helper;

use Zork\Form\Form;
use Zork\Form\Element;
use Zend\Form\Fieldset;
use Zork\Test\PHPUnit\View\Helper\TestCase;

/**
 * FormTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormTest extends TestCase
{

    /**
     * @var string
     */
    protected static $rendererClass = 'Zend\View\Renderer\PhpRenderer';

    /**
     * @var string
     */
    protected static $helperClass = 'Zork\Form\View\Helper\Form';

    /**
     * Test invoke
     */
    public function testInvoke()
    {
        $form = new Form( 'test_form', array(
            'description' => array(
                'label'         => 'form description',
                'translatable'  => false,
                'attributes'    => array(
                    'class'     => 'form-description',
                ),
            )
        ) );

        $fieldset = new Fieldset( 'test_fieldset', array(
            'description' => array(
                'label'         => 'fieldset description',
                'translatable'  => false,
            ),
        ) );

        $text = new Element\Text( 'test_text', array(
            'description'   => 'text description',
            'display_group' => 'display group',
        ) );

        $hidden = new Element\Hidden( 'test_hidden' );
        $search = new Element\Search( 'test_search', array(
            'display_group' => 'display group',
        ) );

        $submit = new Element\Submit( 'test_submit' );

        $fieldset->setLabel( 'fieldset label' );
        $text->setLabel( 'text label' );
        $search->setLabel( 'search label' );
        $submit->setValue( 'submit label' );

        $fieldset->add( $text );
        $fieldset->add( $hidden );
        $fieldset->add( $search );
        $form->add( $fieldset );
        $form->add( $submit );

        $translator = $this->getMock( 'Zend\I18n\Translator\Translator' );

        $translator->expects( $this->at( 1 ) )
                   ->method( 'translate' )
                   ->with( 'fieldset label', 'text domain' )
                   ->will( $this->returnValue( 'fieldset label at text domain' ) );

        $translator->expects( $this->at( 2 ) )
                   ->method( 'translate' )
                   ->with( 'display group', 'text domain' )
                   ->will( $this->returnValue( 'display group at text domain' ) );

        $translator->expects( $this->at( 3 ) )
                   ->method( 'translate' )
                   ->with( 'text label', 'text domain' )
                   ->will( $this->returnValue( 'text label at text domain' ) );

        $translator->expects( $this->at( 4 ) )
                   ->method( 'translate' )
                   ->with( 'text description', 'text domain' )
                   ->will( $this->returnValue( 'text description at text domain' ) );

        $translator->expects( $this->at( 5 ) )
                   ->method( 'translate' )
                   ->with( 'search label', 'text domain' )
                   ->will( $this->returnValue( 'search label at text domain' ) );

        $translator->expects( $this->at( 6 ) )
                   ->method( 'translate' )
                   ->with( 'submit label', 'text domain' )
                   ->will( $this->returnValue( 'submit label at text domain' ) );

        $rendered = $this->helper( $form, $translator, 'text domain' );

        $this->assertTag(
            $formMatcher = array(
                'tag'           => 'form',
                'attributes'    => array(
                    'name'      => 'test_form',
                ),
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'class'         => 'form-description description',
                'ancestor'      => $formMatcher,
                'content'       => 'form description',
            ),
            $rendered
        );

        $this->assertTag(
            $fieldsetMatcher = array(
                'tag'           => 'fieldset',
                'ancestor'      => $formMatcher,
                'attributes'    => array(
                    'name'      => 'test_fieldset',
                ),
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'tag'           => 'legend',
                'parent'        => $fieldsetMatcher,
                'content'       => 'fieldset label at text domain',
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'class'         => 'description',
                'ancestor'      => $fieldsetMatcher,
                'content'       => 'fieldset description',
            ),
            $rendered
        );

        $this->assertTag(
            $displayGroupMatcher = array(
                'tag'           => 'fieldset',
                'ancestor'      => $fieldsetMatcher,
                'attributes'    => array(
                    'class'     => 'display-group',
                ),
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'tag'           => 'label',
                'ancestor'      => $displayGroupMatcher,
                'content'       => 'text label at text domain',
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'tag'           => 'input',
                'ancestor'      => $displayGroupMatcher,
                'attributes'    => array(
                    'type'      => 'text',
                    'name'      => 'test_fieldset[test_text]',
                ),
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'tag'           => 'input',
                'ancestor'      => $fieldsetMatcher,
                'attributes'    => array(
                    'type'      => 'hidden',
                    'name'      => 'test_fieldset[test_hidden]',
                ),
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'tag'           => 'label',
                'ancestor'      => $displayGroupMatcher,
                'content'       => 'search label at text domain',
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'tag'           => 'input',
                'ancestor'      => $displayGroupMatcher,
                'attributes'    => array(
                    'type'      => 'search',
                    'name'      => 'test_fieldset[test_search]',
                ),
            ),
            $rendered
        );

        $this->assertTag(
            array(
                'tag'           => 'input',
                'ancestor'      => $formMatcher,
                'attributes'    => array(
                    'type'      => 'submit',
                    'name'      => 'test_submit',
                    'value'     => 'submit label at text domain',
                ),
            ),
            $rendered
        );
    }

}
