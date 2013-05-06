<?php

namespace Zork\I18n\Translator\Loader;

use Zend\Json\Json;
use Zend\I18n\Exception;
use Zend\I18n\Translator\TextDomain;
use Zend\I18n\Translator\Plural\Rule as PluralRule;
use Zend\I18n\Translator\Loader\FileLoaderInterface;

/**
 * Json file-loader
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @codeCoverageIgnore
 * @deprecated
 */
class Json implements FileLoaderInterface
{

    /**
     * load(): defined by FileLoaderInterface.
     *
     * @see    FileLoaderInterface::load()
     * @param  string $locale
     * @param  string $filename
     * @return TextDomain|null
     * @throws Exception\InvalidArgumentException
     */
    public function load( $locale, $filename )
    {
        if ( ! is_file( $filename ) || ! is_readable( $filename ) )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                'Could not open file %s for reading',
                $filename
            ) );
        }

        $json = file_get_contents( $filename );

        if ( empty( $json ) )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                'File %s is empty',
                $filename
            ) );
        }

        $messages = Json::decode( $json, Json::TYPE_ARRAY );

        if ( ! is_array( $messages ) )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                'Expected an array, but received %s',
                gettype( $messages )
            ) );
        }

        $textDomain = new TextDomain( $messages );

        if ( array_key_exists( '', $textDomain ) )
        {
            if ( isset( $textDomain['']['plural_forms'] ) )
            {
                $textDomain->setPluralRule(
                    PluralRule::fromString( $textDomain['']['plural_forms'] )
                );
            }

            unset( $textDomain[''] );
        }

        return $textDomain;
    }

}
