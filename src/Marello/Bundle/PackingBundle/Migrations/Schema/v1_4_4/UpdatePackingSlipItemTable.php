<?php

namespace Marello\Bundle\PackingBundle\Migrations\Schema\v1_4_4;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

use Marello\Bundle\PackingBundle\Migrations\Schema\MarelloPackingBundleInstaller;

class UpdatePackingSlipItemTable implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updatePackingSlipItemTable($schema);
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
            if ($table->hasIndex('UNIQ_DBF8FC2AE415FB15')) {
                $table->dropIndex('UNIQ_DBF8FC2AE415FB15');
            }
        }
    }
}
