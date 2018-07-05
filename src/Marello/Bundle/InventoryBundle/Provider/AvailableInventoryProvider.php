<?php

namespace Marello\Bundle\InventoryBundle\Provider;

use Marello\Bundle\InventoryBundle\Entity\VirtualInventoryLevel;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\Entity\ProductInterface;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;

class AvailableInventoryProvider
{
    /**
     * @var DoctrineHelper
     */
    protected $doctrineHelper;

    /**
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableInventory($productSku, $salesChannelId)
    {
        $product = $this->getProduct($productSku);
        $salesChannel = $this->getSalesChannel($salesChannelId);
        $salesChannelGroup = $salesChannel->getGroup();
        $result = $this->getVirtualInventoryLevel($product, $salesChannelGroup);

        return ($result) ? $result : 0;
    }

    /**
     * @param $productSku
     * @return ProductInterface|null
     */
    protected function getProduct($productSku)
    {
        return $this->doctrineHelper
            ->getEntityManagerForClass(Product::class)
            ->getRepository(Product::class)
            ->findOneBySku($productSku);
    }

    /**
     * @return SalesChannel|null
     */
    protected function getSalesChannel($salesChannelId)
    {
        return $this->doctrineHelper
            ->getEntityManagerForClass(SalesChannel::class)
            ->getRepository(SalesChannel::class)
            ->find($salesChannelId);
    }

    /**
     * @return VirtualInventoryLevel|null
     */
    protected function getVirtualInventoryLevel($product, $salesChannelGroup)
    {
        return $this->doctrineHelper
            ->getEntityManagerForClass(VirtualInventoryLevel::class)
            ->getRepository(VirtualInventoryLevel::class)
            ->findExistingVirtualInventory($product, $salesChannelGroup);
    }
}
