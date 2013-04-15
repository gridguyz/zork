<?php

namespace Zork\Db\Sql\Predicate;

use Zend\Db\Sql\Predicate\PredicateInterface;

/**
 * SimilarTo
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SimilarTo implements PredicateInterface
{

    /**
     * @var string
     */
    protected $specification = '%1$s SIMILAR TO %2$s';

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var string
     */
    protected $similarTo = '';

    /**
     * @param string $identifier
     * @param string $like
     */
    public function __construct( $identifier = null, $similarTo = null )
    {
        if ( $identifier )
        {
            $this->setIdentifier( $identifier );
        }

        if ( $similarTo )
        {
            $this->setSimilarTo( $similarTo );
        }
    }

    /**
     * @param $identifier
     */
    public function setIdentifier( $identifier )
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param $like
     */
    public function setSimilarTo($like)
    {
        $this->similarTo = $like;
        return $this;
    }

    /**
     * @return string
     */
    public function getSimilarTo()
    {
        return $this->similarTo;
    }

    /**
     * @param $specification
     */
    public function setSpecification( $specification )
    {
        $this->specification = $specification;
        return $this;
    }

    /**
     * @return string
     */
    public function getSpecification()
    {
        return $this->specification;
    }

    /**
     * @return array
     */
    public function getExpressionData()
    {
        return array( array(
            $this->specification,
            array(
                $this->identifier,
                $this->similarTo,
            ),
            array(
                self::TYPE_IDENTIFIER,
                self::TYPE_VALUE,
            ),
        ) );
    }

}
