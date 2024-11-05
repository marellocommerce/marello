<?php

namespace Marello\Bundle\SalesBundle\Migrations\Schema\v1_5;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class AddAssociatedSalesChannelRelation implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('marello_sales_sales_channel');
        if (!$table->hasColumn('associated_sales_channel_id')) {
            $table->addColumn('associated_sales_channel_id', 'integer', ['notnull' => false]);
            $table->addForeignKeyConstraint(
                $schema->getTable('marello_sales_sales_channel'),
                ['associated_sales_channel_id'],
                ['id'],
                ['onDelete' => null, 'onUpdate' => null]
            );
        }
    }
}
