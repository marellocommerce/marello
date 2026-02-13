<?php

namespace Marello\Bundle\CoreBundle\DerivedProperty;

use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;

interface DerivedPropertyAwareInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     */
    public function setDerivedProperty($id);

    public function getEntityType(): string;

    /**
     * @return OrganizationInterface|null
     */
    public function getOrganization();
}
