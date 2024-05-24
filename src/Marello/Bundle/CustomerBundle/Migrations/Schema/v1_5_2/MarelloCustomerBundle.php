<?php

namespace Marello\Bundle\CustomerBundle\Migrations\Schema\v1_5_2;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

use Marello\Bundle\CustomerBundle\Migrations\Schema\MarelloCustomerBundleInstaller;

class MarelloCustomerBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable(MarelloCustomerBundleInstaller::MARELLO_CUSTOMER_TABLE);
        if (!$table->hasColumn('customer_number')) {
            $table->addColumn('customer_number', 'string', ['notnull' => false, 'length' => 255]);
            $table->addUniqueIndex(['customer_number', 'organization_id'], 'marello_customer_numberorgidx');
        }
    }
}
