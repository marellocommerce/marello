<?php

namespace Marello\Bundle\ProductBundle\ImportExport\TemplateFixture;

use Marello\Bundle\PricingBundle\Entity\AssembledPriceList;
use Marello\Bundle\PricingBundle\Entity\ProductPrice;
use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;

class AssembledPriceListFixture extends AbstractTemplateRepository implements TemplateFixtureInterface
{
    protected function createEntity($key): AssembledPriceList
    {
        $product = new Product();
        $product->setSku('sku_001');

        $defaultPrice = new ProductPrice();
        $defaultPrice->setValue('1.5');
        $specialPrice = new ProductPrice();
        $specialPrice->setValue('1.2');
        $specialPrice->setStartDate((new \DateTime())->modify('-1 day'));
        $specialPrice->setEndDate((new \DateTime())->modify('+1 day'));
        $msrpPrice = new ProductPrice();
        $msrpPrice->setValue('1.3');

        $entity = new AssembledPriceList();
        $entity->setCurrency('EUR');
        $entity->setProduct($product);
        $entity->setDefaultPrice($defaultPrice);
        $entity->setSpecialPrice($specialPrice);
        $entity->setMsrpPrice($msrpPrice);

        return $entity;
    }

    public function getEntityClass(): string
    {
        return AssembledPriceList::class;
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
