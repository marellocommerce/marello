<?php

namespace Marello\Bundle\InventoryBundle\Migrations\Schema\v2_8_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\OrderedMigrationInterface;

class UpdateInventoryItemTable implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('marello_inventory_item');
        if (!$table->hasColumn('qty_in_unit')) {
            $table->addColumn('qty_in_unit', 'float', ['notnull' => false]);
        }
    }
}
