<?php

namespace Zork\Form\Element;

use DateTimeZone;

/**
 * Locale
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TimeZone extends Select
{

    /**
     * Is the translator enabled
     *
     * @var bool
     */
    protected $translatorEnabled = false;

    /**
     * @return array
     */
    public function getValueOptions()
    {
        if ( empty( $this->valueOptions ) )
        {
            $this->valueOptions = array();

            foreach ( DateTimeZone::listIdentifiers() as $id )
            {
                $names = explode( '/', $id, 2 );

                if ( isset( $names[1] ) )
                {
                    if ( empty( $this->valueOptions[$names[0]]['label'] ) )
                    {
                        $this->valueOptions[$names[0]]['label'] = $names[0];
                    }

                    $this->valueOptions[$names[0]]['options'][$id] =
                        str_replace(
                            array( '/', '_' ),
                            array( ' / ', ' ' ),
                            $names[1]
                        );
                }
                else
                {
                    $this->valueOptions = array_merge(
                        array( $id => $id ),
                        $this->valueOptions
                    );
                }
            }
        }

        return parent::getValueOptions();
    }

}
