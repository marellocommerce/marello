<?php

namespace Marello\Bundle\PurchaseOrderBundle\Migrations\Schema\v1_4;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class UpdatePurchaseOrderItemTable implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updatePurchaseOrderItemTable($schema);
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function updatePurchaseOrderItemTable(Schema $schema)
    {
        $table = $schema->getTable('marello_purchase_order_item');
        if ($table->hasColumn('purchase_price_value')) {
            $table->changeColumn(
                'purchase_price_value',
                ['notnull' => false]
            );
        }
    }
}
