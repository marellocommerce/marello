<?php

namespace Marello\Bundle\RefundBundle\Migrations\Schema\v1_4_3;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class MarelloRefundBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('marello_refund_item');
        if (!$table->hasColumn('original_item_qty')) {
            $table->addColumn('original_item_qty', 'integer', ['notnull' => false]);
        }
    }
}
