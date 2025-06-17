<?php

namespace Marello\Bundle\CustomerBundle\Migrations\Schema\v1_7_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

use Marello\Bundle\CustomerBundle\Migrations\Schema\MarelloCustomerBundleInstaller;

class MarelloCustomerBundle implements Migration
{
    /**
     * @inheritDoc
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateMarelloCompanyTable($schema);
    }

    protected function updateMarelloCompanyTable(Schema $schema) {
        $table = $schema->getTable(MarelloCustomerBundleInstaller::MARELLO_COMPANY_TABLE);

        if (!$table->hasColumn('sales_rep_id')) {
            $table->addColumn('sales_rep_id', 'integer', ['notnull' => false]);
            $table->addForeignKeyConstraint(
                $schema->getTable('oro_user'),
                ['sales_rep_id'],
                ['id'],
                ['onDelete' => 'SET NULL', 'onUpdate' => null]
            );
        }

        if (!$table->hasColumn('fallback_sales_rep_id')) {
            $table->addColumn('fallback_sales_rep_id', 'integer', ['notnull' => false]);
            $table->addForeignKeyConstraint(
                $schema->getTable('oro_user'),
                ['fallback_sales_rep_id'],
                ['id'],
                ['onDelete' => 'SET NULL', 'onUpdate' => null]
            );
        }
    }
}
