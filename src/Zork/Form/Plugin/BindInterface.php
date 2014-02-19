<?php
namespace Zork\Form\Plugin;

use Zork\Form\Form;

interface BindInterface
{
    public function __construct();
    
    public function bind(Form $form); 
}
