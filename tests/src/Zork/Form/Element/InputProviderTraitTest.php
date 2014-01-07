<?php

namespace Zork\Form\Element;

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * InputProviderTraitTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class InputProviderTraitTest extends TestCase
{

    /**
     * @var Text
     */
    protected $text;

    /**
     * Test getters and setters
     */
    public function testGettersAndSetters()
    {
        $text = new Text;

        $text->setTitle( 'foo' );
        $this->assertEquals( 'foo', $text->getTitle() );
        $this->assertEquals( 'foo', $text->getAttribute( 'title' ) );

        $text->setTitle( null );
        $this->assertNull( $text->getTitle() );
        $this->assertNull( $text->getAttribute( 'title' ) );

        $text->setRequired( true );
        $this->assertTrue( $text->getRequired() );
        $this->assertTrue( $text->getAttribute( 'required' ) );

        $text->setRequired( null );
        $this->assertFalse( $text->getRequired() );
        $this->assertNull( $text->getAttribute( 'required' ) );

        $text->setMultiple( true );
        $this->assertTrue( $text->getMultiple() );
        $this->assertTrue( $text->getAttribute( 'multiple' ) );

        $text->setMultiple( null );
        $this->assertFalse( $text->getMultiple() );
        $this->assertNull( $text->getAttribute( 'multiple' ) );

        $text->setPattern( '[a-z]+' );
        $this->assertEquals( '[a-z]+', $text->getPattern() );
        $this->assertEquals( '[a-z]+', $text->getAttribute( 'pattern' ) );

        $text->setMinlength( 1 );
        $this->assertEquals( 1, $text->getMinlength() );

        $text->setMinlength( null );
        $this->assertNull( $text->getMinlength() );

        $text->setMaxlength( 10 );
        $this->assertEquals( 10, $text->getMaxlength() );
        $this->assertEquals( 10, $text->getAttribute( 'maxlength' ) );

        $text->setMaxlength( null );
        $this->assertNull( $text->getMaxlength() );
        $this->assertNull( $text->getAttribute( 'maxlength' ) );

        $text->setIdentical( 'field' );
        $this->assertEquals( 'field', $text->getIdentical() );

        $text->setIdentical( array( 'foo', 'bar', 'baz' ) );
        $this->assertEquals( array( 'foo', 'bar', 'baz' ),
                             $text->getIdentical() );

        $text->setIdentical( null );
        $this->assertNull( $text->getIdentical() );

        $text->setNotIdentical( 'field' );
        $this->assertEquals( 'field', $text->getNotIdentical() );

        $text->setNotIdentical( array( 'foo', 'bar', 'baz' ) );
        $this->assertEquals( array( 'foo', 'bar', 'baz' ),
                             $text->getNotIdentical() );

        $text->setNotIdentical( null );
        $this->assertNull( $text->getNotIdentical() );

        $text->setAlternate( 'field' );
        $this->assertEquals( 'field', $text->getAlternate() );

        $text->setAlternate( array( 'foo', 'bar', 'baz' ) );
        $this->assertEquals( array( 'foo', 'bar', 'baz' ),
                             $text->getAlternate() );

        $text->setAlternate( null );
        $this->assertNull( $text->getAlternate() );

        $text->setLessThan( 'field' );
        $this->assertEquals( 'field', $text->getLessThan() );

        $text->setLessThan( array( 'foo', 'bar', 'baz' ) );
        $this->assertEquals( array( 'foo', 'bar', 'baz' ),
                             $text->getLessThan() );

        $text->setLessThan( null );
        $this->assertNull( $text->getLessThan() );

        $text->setMoreThan( 'field' );
        $this->assertEquals( 'field', $text->getMoreThan() );

        $text->setMoreThan( array( 'foo', 'bar', 'baz' ) );
        $this->assertEquals( array( 'foo', 'bar', 'baz' ),
                             $text->getMoreThan() );

        $text->setMoreThan( null );
        $this->assertNull( $text->getMoreThan() );

        $text->setForbidden( array( 'foo', 'bar', 'baz' ) );
        $this->assertEquals( array( 'foo', 'bar', 'baz' ),
                             $text->getForbidden() );

        $text->setForbidden( null );
        $this->assertNull( $text->getForbidden() );

        $rpcs = array( 'Rpc1::method1', 'Rpc2::method2' );
        $text->setRpcValidators( new ArrayIterator( $rpcs ) );
        $this->assertEquals( $rpcs, $text->getRpcValidators() );

        $text->setRpcValidators( null );
        $this->assertEmpty( $text->getRpcValidators() );

        $text->setDisplayGroup( 'group1' );
        $this->assertEquals( 'group1', $text->getDisplayGroup() );

        $text->setDisplayGroup( null );
        $this->assertNull( $text->getDisplayGroup() );

        $text->setTranslatorEnabled( true );
        $this->assertTrue( $text->isTranslatorEnabled() );

        $text->setTranslatorEnabled( false );
        $this->assertFalse( $text->isTranslatorEnabled() );

        $text->setTranslatorTextDomain( 'domain1' );
        $this->assertEquals( 'domain1', $text->getTranslatorTextDomain() );

        $text->setTranslatorTextDomain( null );
        $this->assertNull( $text->getTranslatorTextDomain() );

        $text->setInputFilters( array( 'f1' ) );
        $this->assertEquals( array( 'f1' ), $text->getInputFilters() );

        $text->addInputFilters( array( 'f2', 'f3' ) );
        $this->assertEquals( array( 'f1', 'f2', 'f3' ),
                             $text->getInputFilters() );

        $text->addInputFilter( 'f4' );
        $this->assertEquals( array( 'f1', 'f2', 'f3', 'f4' ),
                             $text->getInputFilters() );

        $text->clearInputFilters();
        $this->assertEmpty( $text->getInputFilters() );

        $text->setInputValidators( array( 'v1' ) );
        $this->assertEquals( array( 'v1' ), $text->getInputValidators() );

        $text->addInputValidators( array( 'v2', 'v3' ) );
        $this->assertEquals( array( 'v1', 'v2', 'v3' ),
                             $text->getInputValidators() );

        $text->addInputValidator( 'v4' );
        $this->assertEquals( array( 'v1', 'v2', 'v3', 'v4' ),
                             $text->getInputValidators() );

        $text->clearInputValidators();
        $this->assertEmpty( $text->getInputValidators() );
    }

    /**
     * Test options and specifications
     */
    public function testOptionsAndSpecifications()
    {
        $text = new Text( 'text', array(
            'required'          => true,
            'multiple'          => true,
            'pattern'           => '[a-z]+',
            'minlength'         => 1,
            'maxlength'         => 10,
            'identical'         => 'identical',
            'not_identical'     => 'not_identical',
            'alternate'         => 'alternate',
            'less_than'         => 'less_than',
            'more_than'         => 'more_than',
            'forbidden'         => array( 'forbidden' ),
            'rpc_validators'    => array( 'Rpc1::method1', 'Rpc2', '' ),
            'display_group'     => 'display_group',
            'translatable'      => true,
            'text_domain'       => 'text_domain',
            'filters'           => array( 'filter1' ),
            'validators'        => array( 'validator1' ),
        ) );

        $this->assertTrue( $text->getRequired() );
        $this->assertTrue( $text->getMultiple() );
        $this->assertEquals( '[a-z]+', $text->getPattern() );
        $this->assertEquals( 1, $text->getMinlength() );
        $this->assertEquals( 10, $text->getMaxlength() );
        $this->assertEquals( 'identical', $text->getIdentical() );
        $this->assertEquals( 'not_identical', $text->getNotIdentical() );
        $this->assertEquals( 'alternate', $text->getAlternate() );
        $this->assertEquals( 'less_than', $text->getLessThan() );
        $this->assertEquals( 'more_than', $text->getMoreThan() );
        $this->assertEquals( array( 'forbidden' ), $text->getForbidden() );
        $this->assertEquals( 'display_group', $text->getDisplayGroup() );
        $this->assertTrue( $text->isTranslatorEnabled() );
        $this->assertEquals( 'text_domain', $text->getTranslatorTextDomain() );
        $this->assertEquals( array( 'filter1' ), $text->getInputFilters() );
        $this->assertEquals( array( 'validator1' ), $text->getInputValidators() );
        $this->assertEquals( array( 'Rpc1::method1', 'Rpc2', '' ),
                             $text->getRpcValidators() );

        $spec = $text->getInputSpecification();
        $this->assertInternalType( 'array', $spec );

        $this->assertArrayHasKey( 'name', $spec );
        $this->assertEquals( 'text', $spec['name'] );

        $this->assertArrayHasKey( 'required', $spec );
        $this->assertTrue( $spec['required'] );

        $this->assertArrayHasKey( 'filters', $spec );
        $this->assertNotEmpty( $spec['filters'] );
        $this->assertContains( 'filter1', $spec['filters'] );

        $this->assertArrayHasKey( 'validators', $spec );
        $this->assertNotEmpty( $spec['validators'] );
        $this->assertContains( 'validator1', $spec['validators'] );
        $validators = $spec['validators'];

        /* @var $explode \Zend\Validator\Explode */
        $explode = $this->getValidator( $validators, 'Zend\Validator\Explode' );
        $this->assertTrue( $explode->isValid( array( 'foo', 'bar', 'baz' ) ) );
        $this->assertFalse( $explode->isValid( array( 'fo0', '123', 'baz' ) ) );

        /* @var $length \Zend\Validator\StringLength */
        $length = $this->getValidator( $validators, 'Zend\Validator\StringLength' );
        $this->assertEquals( 1, $length->getMin() );
        $this->assertEquals( 10, $length->getMax() );

        /* @var $identical \Zend\Validator\Identical */
        $identical = $this->getValidator( $validators, 'Zend\Validator\Identical' );
        $this->assertEquals( 'identical', $identical->getToken() );

        /* @var $notIdentical \Zork\Validator\NotIdentical */
        $notIdentical = $this->getValidator( $validators, 'Zork\Validator\NotIdentical' );
        $this->assertEquals( 'not_identical', $notIdentical->getToken() );

        /* @var $alternate \Zork\Validator\Alternate */
        $alternate = $this->getValidator( $validators, 'Zork\Validator\Alternate' );
        $this->assertEquals( 'alternate', $alternate->getToken() );

        /* @var $lessThan \Zork\Validator\LessThan */
        $lessThan = $this->getValidator( $validators, 'Zork\Validator\LessThan' );
        $this->assertEquals( 'less_than', $lessThan->getToken() );

        /* @var $moreThan \Zork\Validator\MoreThan */
        $moreThan = $this->getValidator( $validators, 'Zork\Validator\MoreThan' );
        $this->assertEquals( 'more_than', $moreThan->getToken() );

        /* @var $forbidden \Zork\Validator\Forbidden */
        $forbidden = $this->getValidator( $validators, 'Zork\Validator\Forbidden' );
        $this->assertEquals( array( 'forbidden' ), $forbidden->getHaystack() );

        $this->assertEquals(
            array(
                array(
                    'name'      => 'Zork\Validator\Rpc',
                    'options'   => array(
                        'service'   => 'Rpc1',
                        'method'    => 'method1',
                    ),
                ),
                array(
                    'name'      => 'Zork\Validator\Rpc',
                    'options'   => array(
                        'service'   => 'Rpc2',
                    ),
                ),
            ),
            $text->getRpcValidatorSpecifications()
        );

        $search = new Search( 'search' );
        $spec2 = $search->getInputSpecification();
        $this->assertInternalType( 'array', $spec2 );

        $this->assertArrayHasKey( 'name', $spec2 );
        $this->assertEquals( 'search', $spec2['name'] );
    }

    /**
     * @param   array|\traversable  $validators
     * @param   string              $class
     * @return  mixed
     */
    protected function getValidator( $validators, $class )
    {
        foreach ( $validators as $validator )
        {
            if ( $validator instanceof $class )
            {
                return $validator;
            }
        }

        $this->fail( sprintf(
            'Validators does not contains an instance of "%s"',
            $class
        ) );
    }

}
