<?php

namespace Marello\Bundle\OrderBundle\Migrations\Schema\v3_1_8;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class MarelloOrderBundle implements Migration
{
    /**
     * {@inheritDoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('marello_order_order_item');
        if (!$table->hasColumn('comment')) {
            $table->addColumn('comment', 'text', ['notnull' => false]);
        }
    }
}
