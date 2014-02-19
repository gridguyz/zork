<?php
namespace Zork\Form\Plugin;

use Zork\Form\Form;

interface SetDataInterface
{
    public function __construct();
    
    public function setData(Form $form, $data); 
}
