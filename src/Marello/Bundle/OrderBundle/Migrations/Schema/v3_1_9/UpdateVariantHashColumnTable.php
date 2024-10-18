<?php

namespace marello\src\Marello\Bundle\OrderBundle\Migrations\Schema\v3_1_9;

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
        $table = $schema->getTable('marello_order_order_item');
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
