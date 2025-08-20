<?php

namespace Marello\Bundle\DigitalAssetBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Marello\Bundle\DigitalAssetBundle\Entity\DigitalAssetCategory;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\EntityExtendBundle\Entity\Repository\EnumValueRepository;

class LoadDigitalAssetCategoryData extends AbstractFixture
{
    /** @var ObjectManager $manager */
    protected $manager;

    protected $categories = [
        'Manuals',
        'Images',
        'Certifications',
        'Other',
    ];

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->loadCategories($manager);
    }

    protected function loadCategories(ObjectManager $manager): void
    {
        foreach ($this->categories as $name) {
            $newCategory = new DigitalAssetCategory();
            $newCategory->setName($name);
            $this->manager->persist($newCategory);
        }

        $this->manager->flush();
    }
}