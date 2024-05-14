<?php

namespace Marello\Bundle\CustomerBundle\Migrations\Schema\v1_6;

use Doctrine\DBAL\Schema\Schema;
use Marello\Bundle\CustomerBundle\Migrations\Schema\MarelloCustomerBundleInstaller;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class MarelloCustomerBundle implements Migration
{
    const MARELLO_CUSTOMER_GROUP_TABLE = 'marello_customer_group';
//    const MARELLO_CUSTOMER_JOIN_CUSTOMER_GROUP_TABLE = 'marello_customer_join_customer_group';
//    const MARELLO_COMPANY_JOIN_CUSTOMER_GROUP_TABLE = 'marello_customer_join_customer_group';

    /**
     * @inheritDoc
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->addMarelloCustomerForeignKeys($schema);
        $this->createMarelloCustomerGroupTable($schema);
//        $this->addMarelloCustomerGroupForeignKeys($schema);
//        $this->addMarelloCompanyForeignKeys($schema);
    }

    /**
     * Create marello_customer_group table
     *
     * @param Schema $schema
     */
    protected function createMarelloCustomerGroupTable(Schema $schema)
    {
        $table = $schema->createTable(self::MARELLO_CUSTOMER_GROUP_TABLE);

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('customer_id', 'integer', ['notnull' => false]);
//        $table->addColumn('company_id', 'integer', ['notnull' => false]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
    }

    protected function addMarelloCustomerForeignKeys(Schema $schema) {
        $table = $schema->getTable(MarelloCustomerBundleInstaller::MARELLO_CUSTOMER_TABLE);

        $table->addColumn('customer_group_id', 'integer', ['notnull' => false]);
        $table->addForeignKeyConstraint(
            $table,
            ['customer_group_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }

//    protected function addMarelloCompanyForeignKeys(Schema $schema) {
//        $table = $schema->getTable(MarelloCustomerBundleInstaller::MARELLO_COMPANY_TABLE);
//        $table->addForeignKeyConstraint(
//            $table,
//            ['customer_group_id'],
//            ['id'],
//            ['onDelete' => 'SET NULL', 'onUpdate' => null]
//        );
//    }
}