<?php

namespace Marello\Bundle\PackingBundle\Migrations\Schema\v1_4_3;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

use Marello\Bundle\PackingBundle\Migrations\Schema\MarelloPackingBundleInstaller;

class UpdatePackingSlipAndItemTable implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updatePackingSlipTable($schema);
        $this->updatePackingSlipItemTable($schema);
    }

    /**
     * @param Schema $schema
     * @param QueryBag $queries
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function updatePackingSlipTable(Schema $schema)
    {
        $table = $schema->getTable(MarelloPackingBundleInstaller::MARELLO_PACKING_SLIP_TABLE);
        if (!$table->hasForeignKey('FK_B0E654D953C1C61')) {
            $table->removeForeignKey('FK_B0E654D953C1C61');
            $table->addForeignKeyConstraint(
                $schema->getTable('marello_inventory_allocation'),
                ['source_id'],
                ['id'],
                ['onDelete' => 'SET NULL', 'onUpdate' => null]
            );
        }
    }

    /**
     * @param Schema $schema
     * @param QueryBag $queries
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function updatePackingSlipItemTable(Schema $schema)
    {
        $table = $schema->getTable(MarelloPackingBundleInstaller::MARELLO_PACKING_SLIP_ITEM_TABLE);
        if ($table->hasColumn('order_item_id')) {
            $table->addUniqueIndex(['order_item_id'], 'UNIQ_DBF8FC2AE415FB15', []);
        }
    }
}
