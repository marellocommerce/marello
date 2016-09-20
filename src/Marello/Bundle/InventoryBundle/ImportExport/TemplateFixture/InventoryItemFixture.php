<?php

namespace Marello\Bundle\InventoryBundle\ImportExport\TemplateFixture;

use Marello\Component\Inventory\Model\InventoryItemInterface;
use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;

use Marello\Component\Inventory\Entity\InventoryItem;
use Marello\Component\Product\Entity\Product;
use Marello\Component\Product\Entity\ProductStatus;
use Marello\Component\Sales\Entity\SalesChannel;

class InventoryItemFixture extends AbstractTemplateRepository implements TemplateFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return 'Marello\Component\Inventory\Entity\InventoryItem';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->getEntityData('Macbook');
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity($key)
    {
        return new InventoryItem();
    }

    /**
     * @param string                 $key
     * @param InventoryItemInterface $entity
     */
    public function fillEntityData($key, $entity)
    {
        $warehouseRepo = $this->templateManager
            ->getEntityRepository('Marello\Component\Inventory\Entity\Warehouse');

        switch ($key) {
            case 'Macbook':
                $entity
                    ->setId(1)
                    ->setProduct($this->createProduct($entity))
                    ->setQuantity(12)
                    ->setWarehouse($warehouseRepo->getEntity('main'));
                return;
        }

        parent::fillEntityData($key, $entity);
    }

    /**
     * Create Product
     *
     * @param $item
     *
     * @return Product
     */
    public function createProduct($item)
    {
        $entity = new Product();
        $entity->setName('Woood Coffee Table');
        $entity->setSku('WCT-1');
        $entity->setPrice(399.00);

        $status = new ProductStatus('enabled');
        $entity->setStatus($status);

        $channel = new SalesChannel('magento');
        $entity->addChannel($channel);

        $entity->addInventoryItem($item);
        return $entity;
    }
}
