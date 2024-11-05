<?php

namespace Marello\Bundle\InventoryBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Marello\Bundle\InventoryBundle\Entity\InventoryLevel;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

class UpdateCurrentInventoryLevelsWithOrganization extends AbstractFixture
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->updateCurrentInventoryLevels();
    }

    /**
     * update current InventoryLevels with organization
     */
    public function updateCurrentInventoryLevels()
    {
        $organization = $this->manager->getRepository(Organization::class)->getFirst();

        $inventoryItems = $this->manager
            ->getRepository(InventoryLevel::class)
            ->findBy(['organization' => null]);
        foreach ($inventoryItems as $inventoryItem) {
            $inventoryItem->setOrganization($organization);
            $this->manager->persist($inventoryItem);
        }
        $this->manager->flush();
    }
}
