<?php

namespace Marello\Bundle\CustomerBundle\Migrations\Schema\v1_7;

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
        $table = $schema->getTable(MarelloCustomerBundleInstaller::MARELLO_CUSTOMER_TABLE);
        if (!$table->hasColumn('enabled')) {
            $table->addColumn('enabled', 'boolean', ['notnull' => false, 'default' => false]);
        }
        if (!$table->hasColumn('confirmed')) {
            $table->addColumn('confirmed', 'boolean', ['notnull' => false, 'default' => false]);
        }
        if (!$table->hasColumn('username')) {
            $table->addColumn('username', 'string', ['notnull' => false, 'length' => 255]);
        }
        if (!$table->hasColumn('salt')) {
            $table->addColumn('salt', 'string', ['notnull' => false, 'length' => 255]);
        }
        if (!$table->hasColumn('password')) {
            $table->addColumn('password', 'string', ['notnull' => false, 'length' => 255]);
        }
        if (!$table->hasColumn('confirmation_token')) {
            $table->addColumn('confirmation_token', 'string', ['notnull' => false, 'length' => 255]);
        }
        if (!$table->hasColumn('password_requested')) {
            $table->addColumn('password_requested', 'datetime', ['notnull' => false]);
        }
        if (!$table->hasColumn('password_changed')) {
            $table->addColumn('password_changed', 'datetime', ['notnull' => false]);
        }
        if (!$table->hasColumn('last_login')) {
            $table->addColumn('last_login', 'datetime', ['notnull' => false]);
        }
        if (!$table->hasColumn('login_count')) {
            $table->addColumn('login_count', 'integer', ['default' => '0', 'unsigned' => true]);
        }
    }
}
