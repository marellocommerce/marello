<?php

namespace Marello\Bundle\ProductBundle\ImportExport\TemplateFixture;

use Marello\Bundle\CatalogBundle\Entity\Category;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\Entity\ProductStatus;
use Marello\Bundle\ProductBundle\ImportExport\Helper\ProductAttributesHelper;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\TaxBundle\Entity\TaxCode;
use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\EntityBundle\Helper\FieldHelper;
use Oro\Bundle\EntityConfigBundle\Attribute\Entity\AttributeFamily;
use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;

class ProductFixture extends AbstractTemplateRepository implements TemplateFixtureInterface
{
    public function __construct(
        private FieldHelper $fieldHelper,
        private ProductAttributesHelper $productAttributesHelper
    ) {
    }

    protected function createEntity($key): Product
    {
        $name = new LocalizedFallbackValue();
        $name->setString('Product 001');
        $status = new ProductStatus(ProductStatus::ENABLED);

        $attributeFamilyRepo = $this->templateManager->getEntityRepository(AttributeFamily::class);
        $attributeFamily = $attributeFamilyRepo->getEntity('default');

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

        $image = new File();
        $image->setExternalUrl('https://upload.wikimedia.org/wikipedia/commons/a/a9/Example.jpg');
        $image->setParentEntityClass(Product::class);
        $entity->setImage($image);

        $attributes = $this->productAttributesHelper->getAttributesForExport($attributeFamily);
        $placeholders = $this->productAttributesHelper->getPlaceholders();
        foreach ($attributes as $attribute) {
            $this->fieldHelper->setObjectValue(
                $entity,
                $attribute->getFieldName(),
                $placeholders[$attribute->getType()]
            );
        }

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
