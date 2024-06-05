<?php

namespace Marello\Bundle\ProductBundle\ImportExport\TemplateFixture;

use Marello\Bundle\CatalogBundle\Entity\Category;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\Entity\ProductStatus;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\TaxBundle\Entity\TaxCode;
use Oro\Bundle\EntityConfigBundle\Attribute\Entity\AttributeFamily;
use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;

class ProductFixture extends AbstractTemplateRepository implements TemplateFixtureInterface
{
    protected function createEntity($key): Product
    {
        $name = new LocalizedFallbackValue();
        $name->setString('Product 001');
        $status = new ProductStatus(ProductStatus::ENABLED);
        $attributeFamily = new AttributeFamily();
        $attributeFamily->setCode('marello_default');
        $taxCode = new TaxCode();
        $taxCode->setCode('US');

        $salesChannel1 = new SalesChannel();
        $salesChannel1->setCode('sales_channel_1');
        $salesChannel2 = new SalesChannel();
        $salesChannel2->setCode('sales_channel_2');

        $category1 = new Category();
        $category1->setCode('category_1');
        $category2 = new Category();
        $category2->setCode('category_2');

        $entity = new Product();
        $entity->setSku('sku_001');
        $entity->addName($name);
        $entity->setStatus($status);
        $entity->setAttributeFamily($attributeFamily);
        $entity->setTaxCode($taxCode);
        $entity->addChannel($salesChannel1);
        $entity->addChannel($salesChannel2);
        $entity->addCategory($category1);
        $entity->addCategory($category2);

        return $entity;
    }

    public function getEntityClass(): string
    {
        return Product::class;
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
