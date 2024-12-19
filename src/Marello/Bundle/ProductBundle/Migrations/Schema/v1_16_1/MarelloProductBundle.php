<?php

namespace Marello\Bundle\ProductBundle\Migrations\Schema\v1_16_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class MarelloProductBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('marello_product_product');
        if (!$table->hasColumn('inventory_item_id')) {
            $table->addColumn('inventory_item_id', 'integer', ['notnull' => false]);
        }
    }
}
