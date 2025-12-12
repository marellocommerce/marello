<?php

namespace Marello\Bundle\InvoiceBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Marello\Bundle\InvoiceBundle\Entity\InvoiceItem;
use Marello\Bundle\OrderBundle\Entity\OrderItem;

class UpdateInvoiceItemsWithOrderItem extends AbstractFixture
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
        $this->updateCurrentInvoiceItems();

        $this->manager->flush();
    }

    /**
     * update current InvoiceItems with organization
     */
    public function updateCurrentInvoiceItems()
    {
        $invoiceItems = $this->manager
            ->getRepository(InvoiceItem::class)
            ->findBy(['orderItem' => null]);

        /** @var InvoiceItem $invoiceItem */
        foreach ($invoiceItems as $invoiceItem) {
            $orderItems = $invoiceItem->getInvoice()->getOrder()->getItems();
            /** @var OrderItem $orderItem */
            foreach ($orderItems as $orderItem) {
                if ($orderItem->getProductSku() === $invoiceItem->getProductSku()) {
                    $invoiceItem->setOrderItem($orderItem);
                    $this->manager->persist($invoiceItem);
                }
            }
        }
    }
}
