<?php

namespace Marello\Bundle\InventoryBundle\Migrations\Schema\v2_7;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class UpdateProductTable implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('marello_product_product');
        if (!$table->hasColumn('inventory_item_id')) {
            $table->addColumn('inventory_item_id', 'integer', ['notnull' => false]);
            $table->addUniqueIndex(['inventory_item_id'], 'marello_product_inv_item_uidx');
        }
    }
}
