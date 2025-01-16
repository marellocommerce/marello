<?php

namespace Marello\Bundle\OrderBundle\Migrations\Schema\v3_1_10;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\OrderedMigrationInterface;

class FixOrderBundleMigrationsPart1 implements Migration, OrderedMigrationInterface
{
    /**
     * {@inheritDoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $orderTable = $schema->getTable('marello_order_order');
        if (!$orderTable->hasColumn('user_owner_id')) {
            $orderTable->addColumn('user_owner_id', 'integer', ['notnull' => false]);
        }

        if ($orderTable->hasIndex('uniq_a619dd647be036fc')) {
            $orderTable->dropIndex('uniq_a619dd647be036fc');
            $orderTable->removeForeignKey('fk_a619dd647be036fc');
        }

        if ($orderTable->hasColumn('shipment_id')) {
            $orderTable->dropColumn('shipment_id');
        }

        $orderItemTable = $schema->getTable('marello_order_order_item');
        if (!$orderItemTable->hasColumn('user_owner_id')) {
            $orderItemTable->addColumn('user_owner_id', 'integer', ['notnull' => false]);
        }

        if (!$orderItemTable->hasColumn('item_type')) {
            $orderItemTable->addColumn('item_type', 'string', ['notnull' => false, 'length' => 255]);
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
