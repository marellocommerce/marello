<?php

namespace Marello\Bundle\SalesBundle\Migrations\Schema\v1_5_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class DropOwnerColumn implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('marello_sales_sales_channel');
        if ($table->hasColumn('owner_id')) {
            $table->dropColumn('owner_id');
        }
        if ($table->hasIndex('idx_37c71d17e3c61f9')) {
            $table->dropIndex('idx_37c71d17e3c61f9');
        }

        if ($table->hasForeignKey('fk_37c71d17e3c61f9')) {
            $table->removeForeignKey('fk_37c71d17e3c61f9');
        }

        $queries->addPreQuery(
            new UpdateEntityConfigExtendClassQuery()
        );
    }
}
