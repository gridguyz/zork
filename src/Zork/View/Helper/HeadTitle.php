<?php

namespace Zork\View\Helper;

use Zend\View\Helper\HeadTitle as ZendHeadTitle;

/**
 * HeadTitle
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class HeadTitle extends ZendHeadTitle
{

    /**
     * Slice parts of the title
     *
     * @param int $offset
     * @param int|null $length
     * @param string|null $separator
     * @return string
     */
    public function slice( $offset, $length = null, $separator = null )
    {
        $items = array();

        if ( null !== ( $translator = $this->getTranslator() ) )
        {
            foreach ( $this as $item )
            {
                $items[] = $translator->translate(
                    $item, $this->getTranslatorTextDomain()
                );
            }
        }
        else
        {
            foreach ( $this as $item )
            {
                $items[] = $item;
            }
        }

        if ( ! empty( $offset ) || ! empty( $length ) )
        {
            $items = array_slice( $items, $offset, $length );
        }

        if ( empty( $separator ) )
        {
            $separator = $this->getSeparator();
        }
        else
        {
            $separator = (string) $separator;
        }

        if ( $this->autoEscape )
        {
            $separator  = $this->escape( $separator );
            $items      = array_map( array( $this, 'escape' ), $items );
        }

        return implode(
            ' <span class="separator">' . $separator . '</span> ',
            $items
        );
    }

}
