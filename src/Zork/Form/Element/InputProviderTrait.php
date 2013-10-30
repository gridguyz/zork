<?php

namespace Zork\Form\Element;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\Regex        as RegexValidator;
use Zend\Validator\Explode      as ExplodeValidator;
use Zend\Validator\StringLength as LengthValidator;
use Zend\Validator\Identical    as IdenticalValidator;
use Zork\Validator\NotIdentical as NotIdenticalValidator;
use Zork\Validator\Alternate    as AlternateValidator;
use Zork\Validator\Forbidden    as ForbiddenValidator;
use Zork\Validator\LessThan     as LessThanValidator;
use Zork\Validator\MoreThan     as MoreThanValidator;

/**
 * InputProviderTrait
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait InputProviderTrait
{

    /**
     * Registered filters
     *
     * @var array
     */
    private $inputFilters           = array();

    /**
     * Registered validators
     *
     * @var array
     */
    private $inputValidators        = array();

    /**
     * Display group
     *
     * @var string|null
     */
    protected $displayGroup         = null;

    /**
     * Is the translator enabled
     *
     * @var bool
     */
    protected $translatorEnabled    = true;

    /**
     * Which text-domain should be used on translation
     *
     * @var string|null
     */
    protected $translatorTextDomain = null;

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getAttribute( 'title' );
    }

    /**
     * Set title
     *
     * @param string $title
     * @return \Zork\Form\Element
     */
    public function setTitle( $title )
    {
        return $this->setAttribute(
            'title',
            empty( $title ) ? null : (string) $title
        );
    }

    /**
     * Get required flag
     *
     * @return bool
     */
    public function getRequired()
    {
        $required = $this->getAttribute( 'required' );
        return ! empty( $required );
    }

    /**
     * Set required flag
     *
     * @param bool $required
     * @return \Zork\Form\Element
     */
    public function setRequired( $required )
    {
        return $this->setAttribute( 'required', $required ? true : null );
    }

    /**
     * Get multiple flag
     *
     * @return bool
     */
    public function getMultiple()
    {
        $required = $this->getAttribute( 'multiple' );
        return ! empty( $required );
    }

    /**
     * Set multiple flag
     *
     * @param bool $multiple
     * @return \Zork\Form\Element
     */
    public function setMultiple( $multiple )
    {
        return $this->setAttribute( 'multiple', $multiple ? true : null );
    }

    /**
     * Get pattern
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->getAttribute( 'pattern' );
    }

    /**
     * Set pattern
     *
     * @param string $pattern
     * @return \Zork\Form\Element
     */
    public function setPattern( $pattern )
    {
        return $this->setAttribute( 'pattern', (string) $pattern );
    }

    /**
     * Get pattern-validator (regex / explode - based on multiple)
     *
     * @return null|\Zend\Validator\Regex|\Zend\Validator\Explode
     */
    public function getPatternValidator()
    {
        $pattern = $this->getAttribute( 'pattern' );

        if ( empty( $pattern ) )
        {
            return null;
        }

        $validator = new RegexValidator( '(^' . $pattern . '$)u' );

        if ( $this->getMultiple() )
        {
            $validator = new ExplodeValidator( array(
                'validator' => $validator,
            ) );
        }

        return $validator;
    }

    /**
     * Get min-length
     *
     * @return int
     */
    public function getMinlength()
    {
        $length = $this->getAttribute( 'data-validate-minlength' );
        return null === $length ? null : (int) $length;
    }

    /**
     * Set min-length
     *
     * @param int $length
     * @return \Zork\Form\Element
     */
    public function setMinlength( $length )
    {
        return $this->setAttribute(
            'data-validate-minlength',
            null === $length ? null : (int) $length
        );
    }

    /**
     * Get max-length
     *
     * @return int
     */
    public function getMaxlength()
    {
        $length = $this->getAttribute( 'maxlength' );
        return null === $length ? null : (int) $length;
    }

    /**
     * Set max-length
     *
     * @param int $length
     * @return \Zork\Form\Element
     */
    public function setMaxlength( $length )
    {
        return $this->setAttribute(
            'maxlength',
            null === $length ? null : (int) $length
        );
    }

    /**
     * Get pattern-validator (regex / explode - based on multiple)
     *
     * @return null|\Zend\Validator\StringLength
     */
    public function getLengthValidator()
    {
        $min = $this->getMinlength();
        $max = $this->getMaxlength();

        if ( null === $min && null === $max )
        {
            return null;
        }

        return new LengthValidator( array(
            'min' => $min,
            'max' => $max,
        ) );
    }

    /**
     * Convert token to string
     *
     * @param   null|string|array   $token
     * @return  null|string
     */
    private function convertTokenToString( $token )
    {
        if ( empty( $token ) )
        {
            return null;
        }

        if ( is_array( $token ) )
        {
            $result = null;

            foreach ( $token as $part )
            {
                if ( empty( $result ) )
                {
                    $result = rawurlencode( $part );
                }
                else
                {
                    $result .= '[' . rawurlencode( $part ) . ']';
                }
            }

            return $result;
        }

        return (string) $token;
    }

    /**
     * Convert string to token
     *
     * @param   null|string $tokenString
     * @return  null|string|array
     */
    private function convertStringToToken( $tokenString )
    {
        if ( empty( $tokenString ) )
        {
            return null;
        }

        $token = (string) $tokenString;

        if ( preg_match( '#^[^\[\]]+(\[[^\[\]]*\])+$#', $token ) )
        {
            $tokp  = strtok( $token, '[' );
            $token = array();

            while ( false !== $tokp )
            {
                $token[] = rawurldecode( $tokp );
                $tokp = strtok( '][' );
            }
        }

        return $token;
    }

    /**
     * Get identical-token
     *
     * @return string
     */
    public function getIdentical()
    {
        return $this->convertStringToToken(
            $this->getAttribute( 'data-validate-identical' )
        );
    }

    /**
     * Set identical-token
     *
     * @param string $token
     * @return \Zork\Form\Element
     */
    public function setIdentical( $token )
    {
        return $this->setAttribute(
            'data-validate-identical',
            $this->convertTokenToString( $token )
        );
    }

    /**
     * Get identical-validator
     *
     * @return null|\Zend\Validator\Identical
     */
    public function getIdenticalValidator()
    {
        $token = $this->getIdentical();

        if ( empty( $token ) )
        {
            return null;
        }

        return new IdenticalValidator( $token );
    }

    /**
     * Get not-identical-token
     *
     * @return string
     */
    public function getNotIdentical()
    {
        return $this->convertStringToToken(
            $this->getAttribute( 'data-validate-not-identical' )
        );
    }

    /**
     * Set not-identical-token
     *
     * @param string $token
     * @return \Zork\Form\Element
     */
    public function setNotIdentical( $token )
    {
        return $this->setAttribute(
            'data-validate-not-identical',
            $this->convertTokenToString( $token )
        );
    }

    /**
     * Get not-identical-validator
     *
     * @return null|\Zork\Validator\NotIdentical
     */
    public function getNotIdenticalValidator()
    {
        $token = $this->getNotIdentical();

        if ( empty( $token ) )
        {
            return null;
        }

        return new NotIdenticalValidator( $token );
    }

    /**
     * Get alternate-token
     *
     * @return string
     */
    public function getAlternate()
    {
        return $this->convertStringToToken(
            $this->getAttribute( 'data-validate-alternate' )
        );
    }

    /**
     * Set alternate-token
     *
     * @param string $token
     * @return \Zork\Form\Element
     */
    public function setAlternate( $token )
    {
        return $this->setAttribute(
            'data-validate-alternate',
            $this->convertTokenToString( $token )
        );
    }

    /**
     * Get identical-validator
     *
     * @return null|\Zork\Validator\Alternate
     */
    public function getAlternateValidator()
    {
        $token = $this->getAlternate();

        if ( empty( $token ) )
        {
            return null;
        }

        return new AlternateValidator( $token );
    }

    /**
     * Get less-than-token
     *
     * @return string
     */
    public function getLessThan()
    {
        return $this->convertStringToToken(
            $this->getAttribute( 'data-validate-less-than' )
        );
    }

    /**
     * Set less-than-token
     *
     * @param string $token
     * @return \Zork\Form\Element
     */
    public function setLessThan( $token )
    {
        return $this->setAttribute(
            'data-validate-less-than',
            $this->convertTokenToString( $token )
        );
    }

    /**
     * Get less-than-validator
     *
     * @return null|\Zork\Validator\LessThan
     */
    public function getLessThanValidator()
    {
        $token = $this->getLessThan();

        if ( empty( $token ) )
        {
            return null;
        }

        return new LessThanValidator( $token );
    }

    /**
     * Get more-than-token
     *
     * @return string
     */
    public function getMoreThan()
    {
        return $this->convertStringToToken(
            $this->getAttribute( 'data-validate-more-than' )
        );
    }

    /**
     * Set more-than-token
     *
     * @param string $token
     * @return \Zork\Form\Element
     */
    public function setMoreThan( $token )
    {
        return $this->setAttribute(
            'data-validate-more-than',
            $this->convertTokenToString( $token )
        );
    }

    /**
     * Get less-than-validator
     *
     * @return null|\Zork\Validator\LessThan
     */
    public function getMoreThanValidator()
    {
        $token = $this->getMoreThan();

        if ( empty( $token ) )
        {
            return null;
        }

        return new MoreThanValidator( $token );
    }

    /**
     * Get forbidden-values
     *
     * @return null|array
     */
    public function getForbidden()
    {
        $values = $this->getAttribute( 'data-validate-forbidden' );

        if ( ! empty( $values ) )
        {
            $values = json_decode( $values );
        }

        return $values;
    }

    /**
     * Set forbidden-values
     *
     * @param null|array|\Traversable $values
     * @return \Zork\Form\Element
     */
    public function setForbidden( $values )
    {
        return $this->setAttribute(
            'data-validate-forbidden',
            empty( $values ) ? null : json_encode(
                ArrayUtils::iteratorToArray( $values )
            )
        );
    }

    /**
     * Get identical-validator
     *
     * @return null|\Zork\Validator\Forbidden
     */
    public function getForbiddenValidator()
    {
        $values = $this->getForbidden();

        if ( empty( $values ) )
        {
            return null;
        }

        return new ForbiddenValidator( array(
            'haystack' => $values,
        ) );
    }

    /**
     * Get rpc-validators
     *
     * @return array
     */
    public function getRpcValidators()
    {
        $rpcs = $this->getAttribute( 'data-validate-rpcs' );

        if ( empty( $rpcs ) )
        {
            return array();
        }

        return preg_split( '/\s+/u', $rpcs );
    }

    /**
     * Set rpc-validators
     *
     * @param array|\Traversable|mixed $rpcs
     * @return \Zork\Form\Element
     */
    public function setRpcValidators( $rpcs )
    {
        if ( $rpcs instanceof Traversable )
        {
            $rpcs = ArrayUtils::iteratorToArray( $rpcs );
        }

        if ( ! is_array( $rpcs ) )
        {
            $rpcs = (array) $rpcs;
        }

        return $this->setAttribute(
            'data-validate-rpcs',
            implode( ' ', $rpcs )
        );
    }

    /**
     * Set rpc-validators' specifications
     *
     * @return array
     */
    public function getRpcValidatorSpecifications()
    {
        $specs = array();

        foreach ( $this->getRpcValidators() as $rpc )
        {
            $parts = explode( '::', $rpc, 2 );

            if ( empty( $parts ) )
            {
                continue;
            }

            $options = array(
                'service' => array_shift( $parts ),
            );

            if ( ! empty( $parts ) )
            {
                $options['method'] = array_shift( $parts );
            }

            $specs[] = array(
                'name'      => 'Zork\Validator\Rpc',
                'options'   => $options,
            );
        }

        return $specs;
    }

    /**
     * Get filters
     *
     * @return array
     */
    public function getInputFilters()
    {
        return $this->inputFilters;
    }

    /**
     * Clear filters
     *
     * @return \Zork\Form\Element
     */
    public function clearInputFilters()
    {
        $this->inputFilters = array();
        return $this;
    }

    /**
     * Add filters
     *
     * @param array $filters
     * @return \Zork\Form\Element
     */
    public function addInputFilters( $filters )
    {
        $this->inputFilters = ArrayUtils::merge(
            $this->inputFilters,
            (array) $filters
        );

        return $this;
    }

    /**
     * Add a filter
     *
     * @param array|\Zend\Filter\FilterInterface $filter
     * @return \Zork\Form\Element
     */
    public function addInputFilter( $filter )
    {
        $this->inputFilters[] = $filter;
        return $this;
    }

    /**
     * Set filters
     *
     * @param array $filters
     * @return \Zork\Form\Element
     */
    public function setInputFilters( $filters )
    {
        $this->inputFilters = (array) $filters;
        return $this;
    }

    /**
     * Get validators
     *
     * @return array
     */
    public function getInputValidators()
    {
        return $this->inputValidators;
    }

    /**
     * Clear validators
     *
     * @return \Zork\Form\Element
     */
    public function clearInputValidators()
    {
        $this->inputValidators = array();
        return $this;
    }

    /**
     * Add validators
     *
     * @param array $validators
     * @return \Zork\Form\Element
     */
    public function addInputValidators( $validators )
    {
        $this->inputValidators = ArrayUtils::merge(
            $this->inputValidators,
            (array) $validators
        );

        return $this;
    }

    /**
     * Add a validator
     *
     * @param array|\Zend\Validator\ValidatorInterface $validator
     * @return \Zork\Form\Element
     */
    public function addInputValidator( $validator )
    {
        $this->inputValidators[] = $validator;
        return $this;
    }

    /**
     * Set validators
     *
     * @param array $validators
     * @return \Zork\Form\Element
     */
    public function setInputValidators( $validators )
    {
        $this->inputValidators = (array) $validators;
        return $this;
    }

    /**
     * Get display-group
     *
     * @return string|null
     */
    public function getDisplayGroup()
    {
        return $this->displayGroup;
    }

    /**
     * Set display-group
     *
     * @param string|null $required
     * @return \Zork\Form\Element
     */
    public function setDisplayGroup( $displayGroup )
    {
        $this->options['display_group'] = $this->displayGroup =
                empty( $displayGroup ) ? null : (string) $displayGroup;

        return $this;
    }

    /**
     * Set translator-enabled
     *
     * @param bool $enabled
     * @return \Zork\Form\Element
     */
    public function setTranslatorEnabled( $enabled )
    {
        $this->translatorEnabled = (bool) $enabled;
        return $this;
    }

    /**
     * Set translator text-domain
     *
     * @param null|string $textDomain
     * @return \Zork\Form\Element
     */
    public function setTranslatorTextDomain( $textDomain )
    {
        $this->translatorTextDomain = $textDomain ?: null;
        return $this;
    }

    /**
     * Set options for an element. Accepted options are:
     * - label: label to associate with the element
     * - label_attributes: attributes to use when the label is rendered
     * - required: set required attribute & auto-insert the validator
     * - filters: set additional filters
     * - validators: set additional validators
     *
     * @param  array|\Traversable $options
     * @return \Zork\Form\Element
     * @throws \Zend\Form\Exception\InvalidArgumentException
     */
    public function setOptions( $options )
    {
        parent::setOptions( $options );

        if ( isset( $this->options['required'] ) )
        {
            $this->setRequired( $this->options['required'] );
        }

        if ( isset( $this->options['multiple'] ) )
        {
            $this->setMultiple( $this->options['multiple'] );
        }

        if ( isset( $this->options['pattern'] ) )
        {
            $this->setPattern( $this->options['pattern'] );
        }

        if ( isset( $this->options['minlength'] ) )
        {
            $this->setMinlength( $this->options['minlength'] );
        }

        if ( isset( $this->options['maxlength'] ) )
        {
            $this->setMaxlength( $this->options['maxlength'] );
        }

        if ( isset( $this->options['identical'] ) )
        {
            $this->setIdentical( $this->options['identical'] );
        }

        if ( isset( $this->options['not_identical'] ) )
        {
            $this->setNotIdentical( $this->options['not_identical'] );
        }

        if ( isset( $this->options['alternate'] ) )
        {
            $this->setAlternate( $this->options['alternate'] );
        }

        if ( isset( $this->options['less_than'] ) )
        {
            $this->setLessThan( $this->options['less_than'] );
        }

        if ( isset( $this->options['more_than'] ) )
        {
            $this->setMoreThan( $this->options['more_than'] );
        }

        if ( isset( $this->options['forbidden'] ) )
        {
            $this->setForbidden( $this->options['forbidden'] );
        }

        if ( isset( $this->options['rpc_validators'] ) )
        {
            $this->setRpcValidators( $this->options['rpc_validators'] );
        }

        if ( isset( $this->options['filters'] ) )
        {
            $this->setInputFilters( $this->options['filters'] );
        }

        if ( isset( $this->options['validators'] ) )
        {
            $this->setInputValidators( $this->options['validators'] );
        }

        if ( isset( $this->options['display_group'] ) )
        {
            $this->setDisplayGroup( $this->options['display_group'] );
        }

        if ( isset( $this->options['translatable'] ) )
        {
            $this->setTranslatorEnabled( $this->options['translatable'] );
        }

        if ( isset( $this->options['text_domain'] ) )
        {
            $this->setTranslatorTextDomain( $this->options['text_domain'] );
        }

        return $this;
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInput()}.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        if ( is_callable( 'parent::getInputSpecification' ) )
        {
            $spec = parent::getInputSpecification();
        }
        else
        {
            $spec = array(
                'name'          => $this->getName(),
                'required'      => false,
                'filters'       => array(),
                'validators'    => array(),
            );
        }

        if ( empty( $spec['filters'] ) )
        {
            $spec['filters'] = array();
        }

        if ( empty( $spec['validators'] ) )
        {
            $spec['validators'] = array();
        }

        $spec['required'] = $this->getRequired();

        $spec['filters'] = ArrayUtils::merge(
            $spec['filters'],
            $this->getInputFilters()
        );

        $patternValidator = $this->getPatternValidator();

        if ( $patternValidator )
        {
            $spec['validators'][] = $patternValidator;
        }

        $lengthValidator = $this->getLengthValidator();

        if ( $lengthValidator )
        {
            $spec['validators'][] = $lengthValidator;
        }

        $identicalValidator = $this->getIdenticalValidator();

        if ( $identicalValidator )
        {
            $spec['validators'][] = $identicalValidator;
        }

        $notIdenticalValidator = $this->getNotIdenticalValidator();

        if ( $notIdenticalValidator )
        {
            $spec['validators'][] = $notIdenticalValidator;
        }

        $alternateValidator = $this->getAlternateValidator();

        if ( $alternateValidator )
        {
            $spec['validators'][] = $alternateValidator;
        }

        $lessThanValidator = $this->getLessThanValidator();

        if ( $lessThanValidator )
        {
            $spec['validators'][] = $lessThanValidator;
        }

        $moreThanValidator = $this->getMoreThanValidator();

        if ( $moreThanValidator )
        {
            $spec['validators'][] = $moreThanValidator;
        }

        $forbiddenValidator = $this->getForbiddenValidator();

        if ( $forbiddenValidator )
        {
            $spec['validators'][] = $forbiddenValidator;
        }

        $spec['validators'] = ArrayUtils::merge(
            $spec['validators'],
            $this->getRpcValidatorSpecifications()
        );
        
        $spec['validators'] = ArrayUtils::merge(
            $spec['validators'],
            $this->getInputValidators()
        );
        
        return $spec;
    }

    /**
     * @return bool
     */
    public function isTranslatorEnabled()
    {
        return $this->translatorEnabled;
    }

    /**
     * Get translator text-domain
     *
     * @return string
     */
    public function getTranslatorTextDomain()
    {
        return $this->translatorTextDomain;
    }

}
