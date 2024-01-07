<?php

namespace Marello\Bundle\ProductBundle\Migrations\Schema\v1_15;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class MarelloProductBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateProductSupplierRelationTable($schema);
    }

    protected function updateProductSupplierRelationTable(Schema $schema)
    {
        $table = $schema->getTable('marello_product_prod_supp_rel');
        if (!$table->hasColumn('lead_time')) {
            $table->addColumn('lead_time', 'integer', ['default' => 0]);
        }
    }
}
