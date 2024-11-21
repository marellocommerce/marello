<?php

namespace Marello\Bundle\InventoryBundle\Migrations\Schema\v2_6_12;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\OrderedMigrationInterface;

class UpdateVariantHashColumnTable implements Migration, OrderedMigrationInterface
{
    /**
     * {@inheritDoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('marello_inventory_alloc_item');
        if ($table->hasColumn('variant_hash')) {
            $table->changeColumn('variant_hash', ['notnull' => true]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 15;
    }
}
