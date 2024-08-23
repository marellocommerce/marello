<?php

namespace Marello\Bundle\SupplierBundle\Migrations\Schema\v1_5_2;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

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
        if ($table->hasIndex('UNIQ_16532C7B5E237E06')) {
            $table->dropIndex('UNIQ_16532C7B5E237E06');
            $table->addUniqueIndex(['name', 'organization_id'], 'marello_supplier_supplier_nameorgidx');
        }
    }
}
