<?php

namespace Marello\Bundle\InventoryBundle\Migrations\Schema\v2_6_10;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class MarelloInventoryBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateWarehouseTable($schema);
        $this->updateInventoryBatchTable($schema);
        $this->updateInventoryAllocationTable($schema);
        $this->updateInventoryAllocationItemTable($schema);
    }

    protected function updateWarehouseTable(Schema $schema): void
    {
        $table = $schema->getTable('marello_inventory_warehouse');
        if (!$table->hasColumn('sort_order_ood_loc')) {
            $table->addColumn('sort_order_ood_loc', 'integer', ['notnull' => false]);
        }

        if (!$table->hasColumn('order_on_demand_location')) {
            $table->addColumn('order_on_demand_location', 'boolean', ['notnull' => false]);
        }

        if ($table->hasColumn('sort_order')) {
            $table->dropColumn('sort_order');
        }

        if ($table->hasColumn('notifier')) {
            $table->changeColumn('notifier', ['notnull' => false]);
        }
    }

    protected function updateInventoryBatchTable(Schema $schema): void
    {
        $table = $schema->getTable('marello_inventory_batch');
        if (!$table->hasColumn('order_on_demand_ref')) {
            $table->addColumn('order_on_demand_ref', 'string', ['notnull' => false, 'length' => 255]);
        }

        if (!$table->hasColumn('sell_by_date')) {
            $table->addColumn('sell_by_date', 'datetime', ['notnull' => false, 'comment' => '(DC2Type:datetime)']);
        }
    }

    protected function updateInventoryAllocationTable(Schema $schema): void
    {
        $table = $schema->getTable('marello_inventory_allocation');
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_inventory_allocation'),
            ['source_entity_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }

    protected function updateInventoryAllocationItemTable(Schema $schema): void
    {
        $table = $schema->getTable('marello_inventory_alloc_item');
        if ($table->hasColumn('inventory_batches')) {
            $table->dropColumn('inventory_batches');
        }
        if ($table->hasColumn('order_item_id')) {
            $table->changeColumn('order_item_id', []);
        }
    }
}
