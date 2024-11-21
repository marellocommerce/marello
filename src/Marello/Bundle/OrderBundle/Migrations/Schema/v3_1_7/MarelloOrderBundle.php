<?php

namespace Marello\Bundle\OrderBundle\Migrations\Schema\v3_1_7;

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
        $table = $schema->getTable('marello_order_order');
        if ($table->hasIndex('uniq_a619dd647be036fc')) {
            $table->dropIndex('uniq_a619dd647be036fc');
            $table->removeForeignKey('fk_a619dd647be036fc');
            $table->dropColumn('shipment_id');
        }
    }
}
