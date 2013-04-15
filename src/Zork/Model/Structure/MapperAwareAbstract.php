<?php

namespace Zork\Model\Structure;

use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * DbAdapterAwareAbstract
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class MapperAwareAbstract
       extends StructureAbstract
    implements MapperAwareInterface
{

    use MapperAwareTrait
    {
        MapperAwareTrait::__clone as protected cloneMapper;
    }

    /**
     * Save me
     *
     * @return int Number of affected rows
     */
    public function save()
    {
        return $this->getMapper()
                    ->save( $this );
    }

    /**
     * Delete me
     *
     * @return int Number of affected rows
     */
    public function delete()
    {
        return $this->getMapper()
                    ->delete( $this );
    }

}
