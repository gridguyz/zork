<?php

namespace Zork\Form;

/**
 * @category   Zork
 * @package    Zork_Form
 */
interface FormFactoryAwareInterface
{

    /**
     * Compose a form factory into the object
     *
     * @param Factory $factory
     */
    public function setFormFactory( Factory $factory );

}
