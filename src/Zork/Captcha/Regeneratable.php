<?php

namespace Zork\Captcha;

use Zend\Captcha\Image;

/**
 * Regeneratable
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Regeneratable extends Image
{

    /**
     * Image width
     *
     * @var int
     */
    protected $width = 200;

    /**
     * Image height
     *
     * @var int
     */
    protected $height = 70;

    /**
     * Directory for generated images
     *
     * @var string
     */
    protected $imgDir = 'public/tmp/captcha/';

    /**
     * URL for accessing images
     *
     * @var string
     */
    protected $imgUrl = '/tmp/captcha/';

    /**
     * Image font file
     *
     * @var string
     */
    protected $font = 'public/styles/fonts/captcha.ttf';

    /**
     * Regenerate label
     * <pre>
     * &amp;#x21ba; => &#x21ba;
     * &amp;#x21bb; => &#x21bb;
     * &amp;#x2672; => &#x2672;
     * &amp;#x267a; => &#x267a;
     * &amp;#x267b; => &#x267b;
     * &amp;#x267c; => &#x267c;
     * &amp;#x267d; => &#x267d;
     * </pre>
     *
     * @var string
     */
    protected $regenerateLabel = '&#x21ba;';

    /**
     * Get regenerate label
     *
     * @return string
     */
    public function getRegenerateLabel()
    {
        return $this->regenerateLabel;
    }

    /**
     * Set regenerate label
     *
     * @param string $label
     * @return \Zork\Captcha\Regeneratable
     */
    public function setRegenerateLabel( $label )
    {
        $this->regenerateLabel = (string) $label;
        return $this;
    }

    /**
     * Retrieve captcha ID
     *
     * @return string
     */
    public function getId()
    {
        if ( null !== $this->id &&
             ! preg_match( '/^[a-z0-9_\\\]+$/i', $this->id ) )
        {
            $this->id = null;
        }

        return parent::getId();
    }

    /**
     * Re-generates the word & the image
     *
     * @return void
     */
    public function regenerate()
    {
        $id   = $this->getId();
        $word = $this->generateWord();
        $file = $this->getImgDir() . $id . $this->getSuffix();

        if ( file_exists( $file ) )
        {
            @ unlink( $file );
        }

        $this->setWord( $word );
        $this->generateImage( $id, $word );

        return true;
    }

    /**
     * Get helper name used to render captcha
     *
     * @return string
     */
    public function getHelperName()
    {
        return 'captcha/regeneratable';
    }

}
