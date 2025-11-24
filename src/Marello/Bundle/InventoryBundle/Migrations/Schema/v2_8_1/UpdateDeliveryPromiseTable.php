<?php

namespace Marello\Bundle\InventoryBundle\Migrations\Schema\v2_8_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class UpdateDeliveryPromiseTable implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('marello_inventory_delivery_promise');
        if ($table->hasIndex('uniq_c8edad6877153098')) {
            $table->dropIndex('uniq_c8edad6877153098');
            $table->addUniqueIndex(['code', 'organization_id'], 'marello_inventory_dlvry_prom_codeorgidx');
        }
    }
}
