<?php

namespace AlAya\Common\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SoftDeleteFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias) :string 
    {
        if (!$targetEntity->hasField('deleted')) {
            return '';
        }

        return sprintf("$targetTableAlias.deleted = false or $targetTableAlias.deleted is null");
    }
}
