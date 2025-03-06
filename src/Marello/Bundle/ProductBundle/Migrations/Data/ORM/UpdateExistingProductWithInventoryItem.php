<?php

namespace Marello\Bundle\ProductBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

use Marello\Bundle\InventoryBundle\Entity\InventoryItem;

class UpdateExistingProductWithInventoryItem extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $existingItems = $manager
            ->getRepository(InventoryItem::class)
            ->findAll();

        foreach ($existingItems as $existingItem) {
            $product = $existingItem->getProduct();
            $product->setInventoryItem($existingItem);
            $manager->persist($product);
        }

        $manager->flush();
        $manager->clear();
    }
}
