<?php

namespace Marello\Bundle\InventoryBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Oro\Bundle\OrganizationBundle\Entity\Organization;

use Marello\Bundle\InventoryBundle\Entity\InventoryItem;

class UpdateCurrentInventoryItemsWithOrganization extends AbstractFixture
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
        $this->updateCurrentInventoryItems();
    }

    /**
     * update current InventoryItems with organization
     */
    public function updateCurrentInventoryItems()
    {
        $organization = $this->manager->getRepository(Organization::class)->getFirst();

        $inventoryItems = $this->manager
            ->getRepository(InventoryItem::class)
            ->findBy(['organization' => null]);
        foreach ($inventoryItems as $inventoryItem) {
            $inventoryItem->setOrganization($organization);
            $this->manager->persist($inventoryItem);
        }
        $this->manager->flush();
    }
}
