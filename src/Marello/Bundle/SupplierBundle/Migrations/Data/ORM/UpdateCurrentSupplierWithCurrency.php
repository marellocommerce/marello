<?php

namespace Marello\Bundle\SupplierBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Marello\Bundle\SupplierBundle\Entity\Supplier;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class UpdateCurrentSupplierWithCurrency
{
    use ContainerAwareTrait;

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
        $this->updateCurrentSuppliers();
    }

    /**
     * update current Suppliers with organization
     */
    public function updateCurrentSuppliers()
    {
        $currency = $this->container->get('oro_locale.settings')->getCurrency();

        $suppliers = $this->manager
            ->getRepository('MarelloSupplierBundle:Supplier')
            ->findBy(['currency' => null]);
        /** @var Supplier $supplier */
        foreach ($suppliers as $supplier) {
            $supplier->setCurrency($currency);
            $this->manager->persist($supplier);
        }
        $this->manager->flush();
    }
}
