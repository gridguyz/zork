<?php

namespace Zork\Model;

/**
 * MapperAwareTrait for models
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @implements \Zork\Model\MapperAwareInterface
 */
trait ModelAwareTrait
{

    /**
     * Model instance
     *
     * @var mixed
     */
    private $model;

    /**
     * Get the model object
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set the model object
     *
     * @param mixed $mapper
     * @return \Zork\Model\MapperAwareInterface
     */
    public function setModel( $model = null )
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Clone the model too
     */
    public function __clone()
    {
        if ( null !== $this->model )
        {
            $this->model = clone $this->model;
        }
    }

}
