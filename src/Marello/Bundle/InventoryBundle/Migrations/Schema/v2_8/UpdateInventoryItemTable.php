<?php

namespace Marello\Bundle\InventoryBundle\Migrations\Schema\v2_8;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class UpdateInventoryItemTable implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('marello_inventory_item');
        $table->addColumn('on_hand_promise', 'integer', ['notnull' => false]);
        $table->addColumn('drop_ship_promise', 'integer', ['notnull' => false]);
        $table->addColumn('back_order_promise', 'integer', ['notnull' => false]);
        $table->addColumn('pre_order_promise', 'integer', ['notnull' => false]);
        $table->addColumn('order_on_demand_promise', 'integer', ['notnull' => false]);
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_inventory_delivery_promise'),
            ['on_hand_promise'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('marello_inventory_delivery_promise'),
            ['drop_ship_promise'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('marello_inventory_delivery_promise'),
            ['back_order_promise'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('marello_inventory_delivery_promise'),
            ['pre_order_promise'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('marello_inventory_delivery_promise'),
            ['order_on_demand_promise'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }
}
