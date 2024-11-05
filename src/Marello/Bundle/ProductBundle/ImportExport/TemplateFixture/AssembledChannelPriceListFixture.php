<?php

namespace Marello\Bundle\ProductBundle\ImportExport\TemplateFixture;

use Marello\Bundle\PricingBundle\Entity\AssembledChannelPriceList;
use Marello\Bundle\PricingBundle\Entity\ProductChannelPrice;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;

class AssembledChannelPriceListFixture extends AbstractTemplateRepository implements TemplateFixtureInterface
{
    protected function createEntity($key): AssembledChannelPriceList
    {
        $product = new Product();
        $product->setSku('sku_001');
        $channel = new SalesChannel();
        $channel->setCode('channel_1');

        $defaultPrice = new ProductChannelPrice();
        $defaultPrice->setValue('1.5');
        $specialPrice = new ProductChannelPrice();
        $specialPrice->setValue('1.2');
        $specialPrice->setStartDate((new \DateTime())->modify('-1 day'));
        $specialPrice->setEndDate((new \DateTime())->modify('+1 day'));

        $entity = new AssembledChannelPriceList();
        $entity->setCurrency('EUR');
        $entity->setProduct($product);
        $entity->setChannel($channel);
        $entity->setDefaultPrice($defaultPrice);
        $entity->setSpecialPrice($specialPrice);

        return $entity;
    }

    public function getEntityClass(): string
    {
        return AssembledChannelPriceList::class;
    }

    public function getData()
    {
        return $this->getEntityData('default');
    }

    public function fillEntityData($key, $entity)
    {
        switch ($key) {
            case 'default':
                return;
        }

        parent::fillEntityData($key, $entity);
    }
}
