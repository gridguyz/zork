<?php

namespace Zork\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * FormElement
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormElement extends AbstractHelper
{

    /**
     * @var Form
     */
    protected $formHelper;

    /**
     * @return Form
     */
    protected function getFormHelper()
    {
        if ( $this->formHelper )
        {
            return $this->formHelper;
        }

        if ( method_exists( $this->view, 'plugin' ) )
        {
            $this->formHelper = $this->view->plugin( 'form' );
        }

        if ( ! $this->formHelper instanceof Form )
        {
            $this->formHelper = new Form();
        }

        return $this->formHelper;
    }

    /**
     * @param   \Zend\Form\ElementInterface|null    $element
     * @return  string|\Zork\Form\View\Helper\FormCollection
     */
    public function __invoke( ElementInterface $element = null )
    {
        if ( $element )
        {
            return $this->getFormHelper()
                        ->renderElement( $element );
        }

        return $this;
    }

}
