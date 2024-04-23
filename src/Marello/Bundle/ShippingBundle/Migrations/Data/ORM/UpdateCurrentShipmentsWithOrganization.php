<?php

namespace Marello\Bundle\ShipmentBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Oro\Bundle\OrganizationBundle\Entity\Organization;

use Marello\Bundle\ShippingBundle\Entity\Shipment;

class UpdateCurrentShipmentsWithOrganization extends AbstractFixture
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
        $this->updateCurrentShipments();
    }

    /**
     * update current Shipments with organization
     */
    public function updateCurrentShipments()
    {
        $organization = $this->manager->getRepository(Organization::class)->getFirst();

        $shipments = $this->manager
            ->getRepository(Shipment::class)
            ->findBy(['organization' => null]);
        foreach ($shipments as $shipment) {
            $shipment->setOrganization($organization);
            $this->manager->persist($shipment);
        }
        $this->manager->flush();
    }
}
