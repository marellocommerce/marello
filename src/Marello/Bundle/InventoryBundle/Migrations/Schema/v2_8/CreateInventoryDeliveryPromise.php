<?php

namespace Marello\Bundle\InventoryBundle\Migrations\Schema\v2_8;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\OrderedMigrationInterface;

class CreateInventoryDeliveryPromise implements Migration, OrderedMigrationInterface
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->createMarelloInventoryDeliveryPromise($schema);
        $this->createMarelloInventoryDeliveryPromiseLabel($schema);
        $this->createMarelloInventoryDeliveryPromiseToolTip($schema);

        $this->addMarelloInventoryDeliveryPromiseForeignKeys($schema);
        $this->addMarelloInventoryDeliveryPromiseLabelForeignKeys($schema);
        $this->addMarelloInventoryDeliveryPromiseTooltipForeignKeys($schema);
    }

    /**
     * @param Schema $schema
     */
    protected function createMarelloInventoryDeliveryPromise(Schema $schema)
    {
        $table = $schema->createTable('marello_inventory_delivery_promise');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('code', 'string', ['length' => 255]);
        $table->addColumn('label', 'string', ['length' => 255]);
        $table->addColumn('min_days', 'integer', ['notnull' => false]);
        $table->addColumn('max_days', 'integer', ['notnull' => false]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['code']);
        $table->addIndex(['organization_id']);
    }

    /**
     * @param Schema $schema
     */
    protected function createMarelloInventoryDeliveryPromiseLabel(Schema $schema)
    {
        $table = $schema->createTable('marello_inventory_deli_prom_label');
        $table->addColumn('delivery_promise_id', 'integer');
        $table->addColumn('localized_value_id', 'integer');
        $table->setPrimaryKey(['delivery_promise_id', 'localized_value_id']);
        $table->addUniqueIndex(['localized_value_id']);
    }

    /**
     * @param Schema $schema
     */
    protected function createMarelloInventoryDeliveryPromiseToolTip(Schema $schema)
    {
        $table = $schema->createTable('marello_inventory_deli_prom_tooltip');
        $table->addColumn('delivery_promise_id', 'integer');
        $table->addColumn('localized_value_id', 'integer');
        $table->setPrimaryKey(['delivery_promise_id', 'localized_value_id']);
        $table->addUniqueIndex(['localized_value_id']);
    }

    /**
     * @param Schema $schema
     */
    protected function addMarelloInventoryDeliveryPromiseForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_inventory_delivery_promise');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }

    /**
     * @param Schema $schema
     */
    protected function addMarelloInventoryDeliveryPromiseLabelForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_inventory_deli_prom_label');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_inventory_delivery_promise'),
            ['delivery_promise_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * @param Schema $schema
     */
    protected function addMarelloInventoryDeliveryPromiseTooltipForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_inventory_deli_prom_tooltip');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_inventory_delivery_promise'),
            ['delivery_promise_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 10;
    }
}
