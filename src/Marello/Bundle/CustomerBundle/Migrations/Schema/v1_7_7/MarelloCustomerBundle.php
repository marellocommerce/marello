<?php

namespace Marello\Bundle\CustomerBundle\Migrations\Schema\v1_7_7;

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

        if (!$table->hasColumn('enabled')) {
            $table->addColumn('enabled', 'boolean', ['notnull' => true, 'default' => false]);
        }
    }
}