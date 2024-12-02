<?php

namespace Marello\Bundle\InventoryBundle\Migrations\Schema\v2_6_12;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\OrderedMigrationInterface;

class UpdateAllocationItemTable implements Migration, OrderedMigrationInterface
{
    /**
     * {@inheritDoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('marello_inventory_alloc_item');
        if (!$table->hasColumn('variant_hash')) {
            $table->addColumn('variant_hash', 'string', ['notnull' => false]);
            $sql ='UPDATE marello_inventory_alloc_item SET variant_hash = MD5(product_sku)';
            $queries->addQuery($sql);
        }
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 10;
    }
}
