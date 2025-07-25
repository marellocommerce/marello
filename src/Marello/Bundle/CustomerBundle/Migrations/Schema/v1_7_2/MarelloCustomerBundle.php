<?php

namespace Marello\Bundle\CustomerBundle\Migrations\Schema\v1_7_2;

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
        $this->updateMarelloCustomerTable($schema);
    }

    protected function updateMarelloCustomerTable(Schema $schema) {
        $table = $schema->getTable(MarelloCustomerBundleInstaller::MARELLO_CUSTOMER_TABLE);

        if (!$table->hasColumn('localization_id')) {
            $table->addColumn('localization_id', 'integer', ['notnull' => false]);
            $table->addForeignKeyConstraint(
                $schema->getTable('oro_localization'),
                ['localization_id'],
                ['id'],
                ['onDelete' => null, 'onUpdate' => null]
            );
        }
    }
}
