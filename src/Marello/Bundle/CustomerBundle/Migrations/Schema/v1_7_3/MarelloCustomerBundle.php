<?php

namespace Marello\Bundle\CustomerBundle\Migrations\Schema\v1_7_3;

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

        if (!$table->hasColumn('invoice_email')) {
            $table->addColumn('invoice_email', 'string', ['notnull' => false]);
        }

        if (!$table->hasColumn('send_copy_to_customer')) {
            $table->addColumn('send_copy_to_customer', 'boolean', ['notnull' => true, 'default' => false]);
        }
    }
}