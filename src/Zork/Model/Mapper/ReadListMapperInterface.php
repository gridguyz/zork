<?php

namespace Zork\Model\Mapper;

/**
 * ReadOnlyMapperInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface ReadListMapperInterface
  extends ReadOnlyMapperInterface
{

    /**
     * Find multiple structures
     *
     * @param mixed|null $where
     * @param mixed|null $order
     * @param int|null $limit
     * @param int|null $offset
     * @return \Zork\Model\Structure\StructureAbstract[]
     */
    public function findAll(
        $where  = null,
        $order  = null,
        $limit  = null,
        $offset = null
    );

    /**
     * Find one structure
     *
     * @param mixed|null $where
     * @param mixed|null $order
     * @return \Zork\Model\Structure\StructureAbstract
     */
    public function findOne( $where = null, $order = null );

    /**
     * Get paginator
     *
     * @param mixed|null $where
     * @param mixed|null $order
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator( $where = null, $order = null );

}
