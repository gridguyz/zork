<?php

namespace Zork\Model\Mapper;

/**
 * ReadOnlyMapperInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface ReadOnlyMapperInterface
{

    /**
     * Find a structure
     *
     * @param int|string|array $primaryKeys
     * @return \Zork\Model\Structure\StructureAbstract
     */
    public function find( $primaryKeys );

}
