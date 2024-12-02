<?php

namespace Marello\Bundle\InventoryBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Oro\Bundle\OrganizationBundle\Entity\Organization;

use Marello\Bundle\SalesBundle\Entity\SalesChannelGroup;
use Marello\Bundle\InventoryBundle\Entity\WarehouseGroup;
use Marello\Bundle\InventoryBundle\Entity\WarehouseChannelGroupLink;
use Marello\Bundle\SalesBundle\Migrations\Data\ORM\LoadSalesChannelGroupData;

class CreateDefaultWarehouseChannelGroupLink extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            LoadSalesChannelGroupData::class,
            LoadWarehouseGroupData::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->createDefaultWarehouseChannelGroupLink();
    }

    public function createDefaultWarehouseChannelGroupLink()
    {
        $organization = $this->manager
            ->getRepository(Organization::class)
            ->getFirst();
        $systemWarehouseGroup = $this->manager
            ->getRepository(WarehouseGroup::class)
            ->findOneBy(['system' => true]);
        $systemSalesChannelGroup = $this->manager
            ->getRepository(SalesChannelGroup::class)
            ->findOneBy(['system' => true]);

        $defaultWarehouseChannelGroupLink = new WarehouseChannelGroupLink();
        $defaultWarehouseChannelGroupLink
            ->setSystem(true)
            ->setOrganization($organization)
            ->setWarehouseGroup($systemWarehouseGroup)
            ->addSalesChannelGroup($systemSalesChannelGroup);
        
        $this->manager->persist($defaultWarehouseChannelGroupLink);
        $this->manager->flush();
    }
}
