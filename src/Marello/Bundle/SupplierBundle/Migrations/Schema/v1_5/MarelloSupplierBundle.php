<?php

namespace Marello\Bundle\SupplierBundle\Migrations\Schema\v1_5;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class MarelloSupplierBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->updateMarelloSupplierSupplierTable($schema);
    }

    /**
     * Create marello_supplier_supplier table
     *
     * @param Schema $schema
     */
    protected function updateMarelloSupplierSupplierTable(Schema $schema)
    {
        $table = $schema->getTable('marello_supplier_supplier');

        $table->dropIndex('uniq_16532c7b5e237e06');
        $table->addUniqueIndex(['organization_id', 'name']);
    }
}
