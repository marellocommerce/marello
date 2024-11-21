<?php

namespace Marello\Bundle\InventoryBundle\Migrations\Schema\v2_6_11;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class MarelloInventoryBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateInventoryAllocationItemTable($schema);
    }

    protected function updateInventoryAllocationItemTable(Schema $schema): void
    {
        $table = $schema->getTable('marello_inventory_alloc_item');
        if (!$table->hasColumn('inventory_batches')) {
            $table->addColumn('inventory_batches', 'json', ['notnull' => false, 'comment' => '(DC2Type:json)']);
        }
    }
}
