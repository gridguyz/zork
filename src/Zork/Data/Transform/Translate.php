<?php

namespace Zork\Data\Transform;

use Zend\I18n\Translator\Translator;

/**
 * Translate
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Translate
{

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    protected $translator;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $postfix;

    /**
     * @var string
     */
    protected $textDomain;

    /**
     * @var string|null
     */
    protected $locale;

    /**
     * @param   \Zend\I18n\Translator\Translator    $translator
     * @param   string                              $prefix
     * @param   string                              $postfix
     * @param   string                              $textDomain
     */
    public function __construct( Translator $translator,
                                 $prefix        = '',
                                 $postfix       = '',
                                 $textDomain    = 'default',
                                 $locale        = null )
    {
        $this->translator   = $translator;
        $this->prefix       = $prefix;
        $this->postfix      = $postfix;
        $this->textDomain   = $textDomain;
        $this->locale       = $locale;
    }

    /**
     * @param   string  $key
     * @return  string
     */
    public function __invoke( $key )
    {
        return $this->translator->translate(
            $this->prefix . $key . $this->postfix,
            $this->textDomain,
            $this->locale
        );
    }

}
