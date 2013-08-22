<?php

namespace Zork\I18n\Translator;

use Traversable;
use Zend\I18n\Exception;
use Zend\Stdlib\ArrayUtils;
use Zend\I18n\Translator\Loader\FileLoaderInterface;
use Zend\I18n\Translator\Translator as ZendTranslator;

/**
 * Translator
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Translator extends ZendTranslator
{

    /**
     * Pattern used for loading my messages.
     *
     * @var array
     */
    protected $myPatterns = array();

    /**
     * Schemas used for loading my messages.
     *
     * @var array
     */
    protected $mySchemas = array();

    /**
     * My messages loaded by the translator.
     *
     * @var array
     */
    protected $myMessages = array();

    /**
     * Instantiate a translator
     *
     * @param   array|Traversable   $options
     * @return  Translator
     * @throws  Exception\InvalidArgumentException
     */
    public static function factory( $options )
    {
        if ( $options instanceof Traversable )
        {
            $options = ArrayUtils::iteratorToArray( $options );
        }

        /* @var $translator Translator */
        $translator = parent::factory( $options );

        // file my patterns
        if ( isset( $options['translation_file_my_patterns'] ) )
        {
            if ( ! is_array( $options['translation_file_my_patterns'] ) )
            {
                throw new Exception\InvalidArgumentException(
                    '"translation_file_my_patterns" should be an array'
                );
            }

            $requiredKeys = array( 'type', 'base_dir', 'pattern' );
            foreach ( $options['translation_file_my_patterns'] as $pattern )
            {
                foreach ( $requiredKeys as $key )
                {
                    if ( ! isset($pattern[$key] ) )
                    {
                        throw new Exception\InvalidArgumentException(
                            "'{$key}' is missing for translation my pattern options"
                        );
                    }
                }

                $translator->addMyTranslationFilePattern(
                    $pattern['type'],
                    $pattern['base_dir'],
                    $pattern['pattern']
                );
            }
        }

        return $translator;
    }

    /**
     * Add multiple my-translations with a file pattern.
     *
     * @param   string  $type
     * @param   string  $baseDir
     * @param   string  $pattern
     * @return  Translator
     */
    public function addMyTranslationFilePattern( $type, $baseDir, $pattern )
    {
        $this->myPatterns[] = array(
            'type'    => $type,
            'baseDir' => rtrim( $baseDir, '/' ),
            'pattern' => $pattern,
        );

        return $this;
    }

    /**
     * Add a my-schema
     *
     * @param   string  $schema
     * @return  \Zork\I18n\Translator\Translator
     */
    public function addMySchema( $schema )
    {
        $this->mySchemas[] = (string) $schema;
        return $this;
    }

    /**
     * Set my-schemas
     *
     * @param   string  $schema
     * @return  \Zork\I18n\Translator\Translator
     */
    public function setMySchemas( $schemas )
    {
        if ( $schemas instanceof Traversable )
        {
            $schemas = ArrayUtils::iteratorToArray( $schemas );
        }

        $this->mySchemas = array_filter( array_map( 'strval', (array) $schemas ) );
        return $this;
    }

    /**
     * Get my-schemas
     *
     * @return  array
     */
    public function getMySchemas()
    {
        return $this->mySchemas;
    }

    /**
     * Load messages for a given language and domain.
     *
     * @param   string  $textDomain
     * @param   string  $locale
     * @throws  Exception\RuntimeException
     * @return  void
     */
    protected function loadMessages( $textDomain, $locale )
    {
        $result = parent::loadMessages( $textDomain, $locale );

        if ( ! isset( $this->messages[$textDomain][$locale] ) )
        {
            $this->messages[$textDomain][$locale] = array();
        }

        return $result;
    }

    /**
     * Load messages for a given language and domain.
     *
     * @param   string  $textDomain
     * @param   string  $locale
     * @throws  Exception\RuntimeException
     * @return  void
     */
    protected function loadMyMessages( $textDomain, $locale )
    {
        if ( ! isset( $this->myMessages[$textDomain] ) )
        {
            $this->myMessages[$textDomain] = array();
        }

        $messagesLoaded  = false;
        $messagesLoaded |= $this->loadMyMessagesFromPatterns( $textDomain, $locale );

        if ( ! isset( $this->myMessages[$textDomain][$locale] ) )
        {
            $this->myMessages[$textDomain][$locale] = array();
        }
    }

    /**
     * Load my messages from my patterns.
     *
     * @param   string  $textDomain
     * @param   string  $locale
     * @return  bool
     * @throws  Exception\RuntimeException When specified loader is not a file loader
     */
    protected function loadMyMessagesFromPatterns( $textDomain, $locale )
    {
        $loaders = array();
        $messagesLoaded = false;

        foreach ( $this->mySchemas as $schema )
        {
            foreach ( $this->myPatterns as $pattern )
            {
                if ( empty( $pattern['type'] ) )
                {
                    throw new Exception\RuntimeException(
                        'Must specify loader'
                    );
                }

                $filename = $pattern['baseDir'] . '/' . sprintf(
                    $pattern['pattern'],
                    $schema,
                    $textDomain,
                    $locale
                );

                if ( is_file( $filename ) )
                {
                    $type = $pattern['type'];

                    if ( ! isset( $loaders[$type] ) )
                    {
                        $loaders[$type] = $this->getPluginManager()
                                               ->get( $type );

                        if ( ! $loaders[$type] instanceof FileLoaderInterface )
                        {
                            throw new Exception\RuntimeException(
                                'Specified loader is not a file loader'
                            );
                        }
                    }

                    $myMessages = $loaders[$type]->load( $locale, $filename );

                    if ( isset( $this->myMessages[$textDomain][$locale] ) )
                    {
                        $this->myMessages[$textDomain][$locale]->merge( $myMessages );
                    }
                    else
                    {
                        $this->myMessages[$textDomain][$locale] = $myMessages;
                    }

                    $messagesLoaded = true;
                }
            }
        }

        return $messagesLoaded;
    }

    /**
     * Translate a message.
     *
     * @param   string  $message
     * @param   string  $textDomain
     * @param   string  $locale
     * @return  string
     */
    public function translate( $message,
                               $textDomain  = 'default',
                               $locale      = null )
    {
        return parent::translate(
            $message,
            strstr( $message, '.', true ) ?: $textDomain,
            (string) $locale ?: null
        );
    }

    /**
     * Translate a plural message.
     *
     * @param   string      $singular
     * @param   string      $plural
     * @param   int         $number
     * @param   string      $textDomain
     * @param   string|null $locale
     * @return  string
     * @throws  Exception\OutOfBoundsException
     */
    public function translatePlural( $singular,
                                     $plural,
                                     $number,
                                     $textDomain    = 'default',
                                     $locale        = null )
    {
        return parent::translatePlural(
            $singular,
            $plural,
            $number,
            strstr( $singular, '.', true ) ?: strstr( $plural, '.', true ) ?: $textDomain,
            (string) $locale ?: null
        );
    }

    /**
     * Get a translated message.
     *
     * @triggers    getTranslatedMessage.missing-translation
     * @param       string  $message
     * @param       string  $locale
     * @param       string  $textDomain
     * @return      string|null
     */
    protected function getTranslatedMessage( $message,
                                             $locale,
                                             $textDomain = 'default' )
    {
        if ( $message === '' )
        {
            return '';
        }

        if ( ! isset( $this->myMessages[$textDomain][$locale] ) )
        {
            $this->loadMyMessages( $textDomain, $locale );
        }

        if ( isset( $this->myMessages[$textDomain][$locale][$message] ) )
        {
            return $this->myMessages[$textDomain][$locale][$message];
        }

        return parent::getTranslatedMessage( $message, $locale, $textDomain );
    }

}
