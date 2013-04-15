<?php

namespace Zork\Form\Element;

use Zend\Form\Element\File as ElementBase;
use Zend\InputFilter\InputProviderInterface;
use Zork\Form\TranslatorSettingsAwareInterface;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class File extends ElementBase
        implements InputProviderInterface,
                   TranslatorSettingsAwareInterface
{

    use InputProviderTrait
    {
        InputProviderTrait::setOptions as private setOptionsInputProviderTrait;
    }

    /**
     * @const null
     */
    const ACCEPT_ANY    = null;

    /**
     * @const string
     */
    const ACCEPT_AUDIOS = 'audio/*';

    /**
     * @const string
     */
    const ACCEPT_IMAGES = 'image/*';

    /**
     * @const string
     */
    const ACCEPT_VIDEOS = 'video/*';

    /**
     * Get accept
     *
     * @return string
     */
    public function getAccept()
    {
        return $this->getAttribute( 'accept' );
    }

    /**
     * Set accept
     *
     * @param string $accept
     * @return \Zork\Form\Element\File
     */
    public function setAccept( $accept )
    {
        if ( is_array( $accept ) )
        {
            $accept = implode( ',', $accept );
        }

        $accept = (string) $accept;

        switch ( strtolower( $accept ) )
        {
            case 'audio':
            case 'audios':
                $accept = self::ACCEPT_AUDIOS;
                break;

            case 'image':
            case 'images':
                $accept = self::ACCEPT_IMAGES;
                break;

            case 'video':
            case 'videos':
                $accept = self::ACCEPT_VIDEOS;
                break;
        }

        return $this->setAttribute( 'accept', $accept ?: null );
    }

    /**
     * Get value
     *
     * @return \Zork\Form\Element\File\Value
     */
    public function getValue()
    {
        $value = parent::getValue();
        return empty( $value ) ? null : new File\Value( $value );
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
        $this->setOptionsInputProviderTrait( $options );

        if ( isset( $this->options['accept'] ) )
        {
            $this->setAccept( $this->options['accept'] );
        }
    }

}
