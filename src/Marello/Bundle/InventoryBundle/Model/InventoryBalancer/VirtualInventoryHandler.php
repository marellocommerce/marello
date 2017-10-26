<?php

namespace Marello\Bundle\InventoryBundle\Model\InventoryBalancer;

use Doctrine\Common\Persistence\ObjectManager;

use Marello\Bundle\InventoryBundle\Entity\VirtualInventoryLevel;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\Entity\ProductInterface;
use Marello\Bundle\SalesBundle\Entity\SalesChannelGroup;
use Marello\Bundle\InventoryBundle\Model\InventoryBalancer\VirtualInventoryFactory;

class VirtualInventoryHandler
{
    /** @var VirtualInventoryFactory $triggerFactory */
    protected $virtualInventoryFactory;

    /** @var ObjectManager $objectManager */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     * @param VirtualInventoryFactory $virtualInventoryFactory
     */
    public function __construct(
        ObjectManager $objectManager,
        VirtualInventoryFactory $virtualInventoryFactory
    ) {
        $this->objectManager = $objectManager;
        $this->virtualInventoryFactory = $virtualInventoryFactory;
    }

    /**
     * @param Product $product
     * @param SalesChannelGroup $group
     * @param $inventoryQty
     * @return VirtualInventoryLevel
     */
    public function createVirtualInventory(Product $product, SalesChannelGroup $group, $inventoryQty)
    {
        return $this->virtualInventoryFactory->create($product, $group, $inventoryQty);
    }

    /**
     * Save virtual inventory
     * @param VirtualInventoryLevel $level
     * @param bool $force
     */
    public function saveVirtualInventory(VirtualInventoryLevel $level, $force = false)
    {
        $existingLevel = $this->findExistingVirtualInventory($level->getProduct(), $level->getSalesChannelGroup());

        if ($existingLevel) {
            if (!$this->isLevelChanged($existingLevel, $level) && !$force) {
                return;
            }

            $existingLevel->setInventory($level->getInventory());
            $level = $existingLevel;
        }

        $this->objectManager->persist($level);
        $this->objectManager->flush();
    }

    /**
     * Check whether the existing level has changed inventory
     * @param VirtualInventoryLevel $existingLevel
     * @param VirtualInventoryLevel $level
     * @return bool
     */
    private function isLevelChanged($existingLevel, $level)
    {
        return ((float)$existingLevel->getInventory() !== (float)$level->getInventory());
    }

    /**
     * Find existing VirtualInventoryLevel
     * @param ProductInterface $product
     * @param SalesChannelGroup $group
     * @return VirtualInventoryLevel|object
     */
    private function findExistingVirtualInventory(ProductInterface $product, SalesChannelGroup $group)
    {
        $repository = $this->objectManager->getRepository(VirtualInventoryLevel::class);
        $existingLevel = $repository->findOneBy([
            'salesChannelGroup' => $group,
            'product' => $product
        ]);

        return $existingLevel;
    }
}
