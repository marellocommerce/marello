<?php

namespace Marello\Bundle\CustomerBundle\Migrations\Schema\v1_5_3;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

use Marello\Bundle\CustomerBundle\Migrations\Schema\MarelloCustomerBundleInstaller;

class MarelloCustomerBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable(MarelloCustomerBundleInstaller::MARELLO_CUSTOMER_TABLE);
        if (!$table->hasColumn('email_lowercase')) {
            $table->addColumn('email_lowercase', 'string', ['notnull' => false, 'length' => 255]);
        }

        if (!$table->hasColumn('is_hidden')) {
            $table->addColumn('is_hidden', 'boolean', ['default' => false]);
        }

        $sql = sprintf('UPDATE %s SET email_lowercase = LOWER(email);',
            MarelloCustomerBundleInstaller::MARELLO_CUSTOMER_TABLE
        );
        $queries->addPostQuery($sql);
    }
}
