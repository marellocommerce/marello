<?php

namespace Marello\Bundle\ReturnBundle\Tests\Functional\DataFixtures;

use Marello\Bundle\InventoryBundle\Entity\AllocationItem;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\OrderBundle\Tests\Functional\DataFixtures\LoadOrderData;

class LoadUnReturnedOrderData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
{
    use ContainerAwareTrait;

    public function getDependencies()
    {
        return [
            LoadOrderData::class
        ];
    }

    public function load(ObjectManager $manager)
    {
        if ($this->hasReference('marello_order_unreturned')) {
            /** @var Order $order */
            $order = $this->getReference('marello_order_unreturned');
            $allocationProvider = $this
                ->container
                ->get('Marello\Bundle\InventoryBundle\Provider\InventoryAllocationProvider');
            $allocationProvider->allocateOrderToWarehouses($order);

            foreach ($order->getItems() as $item) {
                /** @var AllocationItem $allocItem */
                $allocItems = $manager->getRepository(AllocationItem::class)->findBy(['orderItem' => $item->getId()]);
                foreach ($allocItems as $allocItem) {
                    $allocItem->setQuantityConfirmed($item->getQuantity());
                    $manager->persist($allocItem);
                }
            }
            $manager->flush();
        }

    }
}
