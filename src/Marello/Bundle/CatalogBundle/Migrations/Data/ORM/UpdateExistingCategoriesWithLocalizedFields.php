<?php

namespace Marello\Bundle\CatalogBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Marello\Bundle\CatalogBundle\Entity\Category;

class UpdateExistingCategoriesWithLocalizedFields extends AbstractFixture
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var Category[] $categories */
        $categories = $manager
            ->getRepository(Category::class)
            ->createQueryBuilder('c')
            ->leftJoin('c.names', 'n')
            ->having('COUNT(n.id) = 0')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();

        if (count($categories) === 0) {
            return;
        }

        foreach ($categories as $category) {
            // The old denormalized name field will be used as the default name
            $category->setDefaultName($category->getDenormalizedDefaultName());
            
            // The old denormalized description field will be used as the default description
            $description = $category->getDenormalizedDefaultDescription();
            if ($description) {
                $category->setDefaultDescription($description);
            }
            
            $manager->persist($category);
        }

        $manager->flush();
    }
}
