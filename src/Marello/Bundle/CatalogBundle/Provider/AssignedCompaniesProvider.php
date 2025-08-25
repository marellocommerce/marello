<?php

namespace Marello\Bundle\CatalogBundle\Provider;

use Doctrine\Persistence\ObjectManager;

use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

use Marello\Bundle\CatalogBundle\Entity\Category;
use Marello\Bundle\CustomerBundle\Entity\Company;

class AssignedCompaniesProvider
{
    public function __construct(
        protected ObjectManager $manager,
        protected AclHelper $aclHelper
    ) {
    }

    public function getCompaniesIds(Category $category)
    {
        $ids = [];
        $category
            ->getCompanies()
            ->map(function (Company $company) use (&$ids) {
                $ids[] = $company->getId();
            });
        return $ids;
    }
}
