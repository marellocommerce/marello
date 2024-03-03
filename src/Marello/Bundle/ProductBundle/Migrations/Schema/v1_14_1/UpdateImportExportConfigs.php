<?php

namespace Marello\Bundle\ProductBundle\Migrations\Schema\v1_14_1;

use Doctrine\DBAL\Schema\Schema;
use Marello\Bundle\PricingBundle\Entity\AssembledChannelPriceList;
use Marello\Bundle\PricingBundle\Entity\AssembledPriceList;
use Marello\Bundle\PricingBundle\Entity\PriceType;
use Marello\Bundle\PricingBundle\Entity\ProductChannelPrice;
use Marello\Bundle\PricingBundle\Entity\ProductPrice;
use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigFieldValueQuery;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class UpdateImportExportConfigs implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateProductEntity($queries);
        $this->updateAssembledPriceListEntity($queries);
        $this->updateAssembledChannelPriceListEntity($queries);
        $this->updateProductPriceEntity($queries);
        $this->updateProductChannelPriceEntity($queries);
        $this->updatePriceTypeEntity($queries);
    }

    private function updateProductEntity(QueryBag $queries): void
    {
        $this->updateField($queries, Product::class, 'status', 'excluded', false);
        $this->updateField($queries, Product::class, 'status', 'order', 30);
        $this->updateField($queries, Product::class, 'status', 'full', false);
        $this->updateField($queries, Product::class, 'channels', 'excluded', false);
        $this->updateField($queries, Product::class, 'channels', 'order', 40);
        $this->updateField($queries, Product::class, 'channels', 'full', false);
        $this->updateField($queries, Product::class, 'taxCode', 'excluded', false);
        $this->updateField($queries, Product::class, 'taxCode', 'order', 40);
        $this->updateField($queries, Product::class, 'taxCode', 'full', false);
        $this->updateField($queries, Product::class, 'categories', 'excluded', false);
        $this->updateField($queries, Product::class, 'categories', 'order', 50);
        $this->updateField($queries, Product::class, 'categories', 'full', false);
        $this->updateField($queries, Product::class, 'attributeFamily', 'order', 35);
        $this->updateField($queries, Product::class, 'attributeFamily', 'full', false);
        $this->updateField($queries, Product::class, 'createdAt', 'excluded', true);
        $this->updateField($queries, Product::class, 'updatedAt', 'excluded', true);
    }

    private function updateAssembledPriceListEntity(QueryBag $queries): void
    {
        $this->updateField($queries, AssembledPriceList::class, 'currency', 'identity', false);
        $this->updateField($queries, AssembledPriceList::class, 'currency', 'order', 10);
        $this->updateField($queries, AssembledPriceList::class, 'product', 'identity', false);
        $this->updateField($queries, AssembledPriceList::class, 'product', 'order', 5);
        $this->updateField($queries, AssembledPriceList::class, 'product', 'full', false);
        $this->updateField($queries, AssembledPriceList::class, 'defaultPrice', 'identity', false);
        $this->updateField($queries, AssembledPriceList::class, 'defaultPrice', 'order', 15);
        $this->updateField($queries, AssembledPriceList::class, 'defaultPrice', 'full', true);
        $this->updateField($queries, AssembledPriceList::class, 'specialPrice', 'identity', false);
        $this->updateField($queries, AssembledPriceList::class, 'specialPrice', 'order', 20);
        $this->updateField($queries, AssembledPriceList::class, 'specialPrice', 'full', true);
        $this->updateField($queries, AssembledPriceList::class, 'msrpPrice', 'identity', false);
        $this->updateField($queries, AssembledPriceList::class, 'msrpPrice', 'order', 25);
        $this->updateField($queries, AssembledPriceList::class, 'msrpPrice', 'full', true);
    }

    private function updateAssembledChannelPriceListEntity(QueryBag $queries): void
    {
        $this->updateField($queries, AssembledChannelPriceList::class, 'currency', 'identity', false);
        $this->updateField($queries, AssembledChannelPriceList::class, 'currency', 'order', 10);
        $this->updateField($queries, AssembledChannelPriceList::class, 'product', 'identity', false);
        $this->updateField($queries, AssembledChannelPriceList::class, 'product', 'order', 5);
        $this->updateField($queries, AssembledChannelPriceList::class, 'product', 'full', false);
        $this->updateField($queries, AssembledChannelPriceList::class, 'defaultPrice', 'identity', false);
        $this->updateField($queries, AssembledChannelPriceList::class, 'defaultPrice', 'order', 10);
        $this->updateField($queries, AssembledChannelPriceList::class, 'defaultPrice', 'full', true);
        $this->updateField($queries, AssembledChannelPriceList::class, 'specialPrice', 'identity', false);
        $this->updateField($queries, AssembledChannelPriceList::class, 'specialPrice', 'order', 20);
        $this->updateField($queries, AssembledChannelPriceList::class, 'specialPrice', 'full', true);
    }

    private function updateProductPriceEntity(QueryBag $queries): void
    {
        $this->updateField($queries, ProductPrice::class, 'product', 'excluded', true);
        $this->updateField($queries, ProductPrice::class, 'product', 'identity', false);
        $this->updateField($queries, ProductPrice::class, 'value', 'order', 40);
        $this->updateField($queries, ProductPrice::class, 'currency', 'excluded', true);
        $this->updateField($queries, ProductPrice::class, 'currency', 'identity', false);
        $this->updateField($queries, ProductPrice::class, 'type', 'excluded', true);
    }

    private function updateProductChannelPriceEntity(QueryBag $queries): void
    {
        $this->updateField($queries, ProductChannelPrice::class, 'product', 'excluded', true);
        $this->updateField($queries, ProductChannelPrice::class, 'channel', 'excluded', true);
        $this->updateField($queries, ProductChannelPrice::class, 'value', 'order', 40);
        $this->updateField($queries, ProductChannelPrice::class, 'currency', 'excluded', true);
        $this->updateField($queries, ProductChannelPrice::class, 'currency', 'identity', false);
        $this->updateField($queries, ProductChannelPrice::class, 'type', 'excluded', true);
    }

    private function updatePriceTypeEntity(QueryBag $queries): void
    {
        $this->updateField($queries, PriceType::class, 'name', 'identity', true);
    }

    private function updateField(QueryBag $queries, string $entity, string $field, string $code, mixed $value): void
    {
        $query = new UpdateEntityConfigFieldValueQuery($entity, $field, 'importexport', $code, $value);
        $queries->addQuery($query);
    }
}
