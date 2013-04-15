<?php

namespace Zork\Form\Element;

use Zend\Form\Element\Select as ElementBase;
use Zend\InputFilter\InputProviderInterface;
use Zork\Form\TranslatorSettingsAwareInterface;
use Zend\Validator\Explode as ExplodeValidator;
use Zend\Validator\InArray as InArrayValidator;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Select extends ElementBase
          implements InputProviderInterface,
                     TranslatorSettingsAwareInterface
{

    use InputProviderTrait;

    /**
     * Update InArrayValidator validator haystack
     *
     * @return mixed
     */
    private function appendEmptyInArray( $validator )
    {
        if ( null !== $validator )
        {
            if ( $validator instanceof InArrayValidator )
            {
                $inArray = $validator;
            }

            if ( $validator instanceof ExplodeValidator &&
                 $validator->getValidator() instanceof InArrayValidator )
            {
                $inArray = $validator->getValidator();
            }

            if ( ! empty( $inArray ) && isset( $this->emptyOption ) )
            {
                $inArray->setHaystack( array_merge(
                    array( '' ),
                    $inArray->getHaystack()
                ) );
            }
        }

        return $validator;
    }

    /**
     * @param  array $options
     * @return Select
     */
    public function setValueOptions( array $options )
    {
        parent::setValueOptions( $options );
        $this->appendEmptyInArray( $this->validator );
        return $this;
    }

    /**
     * Set the string for an empty option (can be empty string). If set to null, no option will be added
     *
     * @param  string|null $emptyOption
     * @return Select
     */
    public function setEmptyOption( $emptyOption )
    {
        parent::setEmptyOption( $emptyOption );
        $this->appendEmptyInArray( $this->validator );
        return $this;
    }

    /**
     * Get validator
     *
     * @return \Zend\Validator\ValidatorInterface
     */
    protected function getValidator()
    {
        $validator = parent::getValidator();
        return $this->appendEmptyInArray( $validator );
    }

}
