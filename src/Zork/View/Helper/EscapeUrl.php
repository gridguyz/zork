<?php

namespace Zork\View\Helper;

use Zend\View\Helper\EscapeUrl as EscapeBase;

/**
 * Helper for escaping values
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class EscapeUrl extends EscapeBase
{

    use EscapeTrait;

}
