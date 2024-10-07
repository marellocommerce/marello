<?php

namespace Marello\Bundle\CustomerBundle\Migrations\Schema\v1_5_4;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

use Marello\Bundle\CustomerBundle\Migrations\Schema\MarelloCustomerBundleInstaller;

class MarelloCustomerBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable(MarelloCustomerBundleInstaller::MARELLO_CUSTOMER_TABLE);
        if ($table->hasIndex('UNIQ_AD0CE5A2CB134313')) {
            $table->dropIndex('UNIQ_AD0CE5A2CB134313');
        }

        if ($table->hasIndex('UNIQ_AD0CE5A24D4CFF2B')) {
            $table->dropIndex('UNIQ_AD0CE5A24D4CFF2B');
        }

        if ($table->hasColumn('primary_address_id')) {
            $table->addIndex(['primary_address_id']);
        }

        if ($table->hasColumn('shipping_address_id')) {
            $table->addIndex(['shipping_address_id']);
        }
    }
}
