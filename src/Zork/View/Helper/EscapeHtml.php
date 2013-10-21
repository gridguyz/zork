<?php

namespace Zork\View\Helper;

use Zend\View\Helper\EscapeHtml as EscapeBase;

/**
 * Helper for escaping values
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class EscapeHtml extends EscapeBase
{

    use EscapeTrait;

}
