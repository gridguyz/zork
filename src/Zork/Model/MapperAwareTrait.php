<?php

namespace Zork\Model;

/**
 * MapperAwareTrait for models
 *
 * implements MapperAwareInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait MapperAwareTrait
{

    /**
     * Mapper instance
     *
     * @var \Zork\Model\Mapper\ReadOnlyMapperInterface
     */
    private $mapper;

    /**
     * Get the mapper object
     *
     * @return \Zork\Model\Mapper\ReadOnlyMapperInterface
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * Set the mapper object
     *
     * @param \Zork\Model\Mapper\ReadOnlyMapperInterface $mapper
     * @return \Zork\Model\MapperAwareInterface
     */
    public function setMapper( $mapper = null )
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * Clone the mapper too
     */
    public function __clone()
    {
        if ( null !== $this->mapper )
        {
            $this->mapper = clone $this->mapper;
        }
    }

}
