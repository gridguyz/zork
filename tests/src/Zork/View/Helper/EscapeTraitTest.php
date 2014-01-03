<?php

namespace Zork\View\Helper;

use Zork\Test\PHPUnit\View\Helper\TestCase;

/**
 * EscapeTraitTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\View\Helper\EscapeTrait
 */
class EscapeTraitTest extends TestCase
{

    /**
     * @var string
     */
    protected static $helperClass = 'Zork\View\Helper\EscapeHtml';

    /**
     * Test locales
     */
    public function testCallWithArray()
    {
        $this->assertEmpty( $this->helper( array( '' ) ) );
    }

}
