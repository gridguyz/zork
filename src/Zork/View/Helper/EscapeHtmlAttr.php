<?php

namespace Zork\View\Helper;

use Zend\View\Helper\EscapeHtmlAttr as EscapeBase;

/**
 * Helper for escaping values
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class EscapeHtmlAttr extends EscapeBase
{

    use EscapeTrait;

}
