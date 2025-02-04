<?php

namespace Marello\Bundle\CustomerBundle\Migrations\Schema\v1_6_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

use Marello\Bundle\CustomerBundle\Migrations\Schema\MarelloCustomerBundleInstaller;

class UpdateCompanyTable implements Migration
{
    /**
     * @inheritDoc
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateMarelloCompanyTable($schema);
    }

    /**
     * Create marello_customer_company table
     *
     * @param Schema $schema
     */
    protected function updateMarelloCompanyTable(Schema $schema)
    {
        $table = $schema->getTable(MarelloCustomerBundleInstaller::MARELLO_COMPANY_TABLE);
        if (!$table->hasColumn('discount_percentage')) {
            $table->addColumn('discount_percentage', 'float', ['notnull' => true]);
        }
    }
}