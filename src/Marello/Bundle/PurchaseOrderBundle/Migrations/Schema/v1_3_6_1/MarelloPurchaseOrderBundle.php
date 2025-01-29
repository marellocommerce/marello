<?php

namespace Marello\Bundle\PurchaseOrderBundle\Migrations\Schema\v1_3_6;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class MarelloPurchaseOrderBundle implements Migration
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
        if (!$table->hasColumn('requested_delivery_date')) {
            $table->addColumn('requested_delivery_date', 'datetime', ['notnull' => false]);
        }
    }
}
