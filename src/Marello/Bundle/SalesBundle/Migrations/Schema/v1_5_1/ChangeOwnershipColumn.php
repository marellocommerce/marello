<?php

namespace Marello\Bundle\SalesBundle\Migrations\Schema\v1_5_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class ChangeOwnershipColumn implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('marello_sales_sales_channel');
        if ($table->hasColumn('owner_id')) {
            if (!$table->hasColumn('organization_id')) {
                $table->addColumn('organization_id', 'integer', ['notnull' => false]);
                $sql = sprintf(
                    'UPDATE %s SET organization_id = owner_id;',
                    'marello_sales_sales_channel'
                );
                $queries->addPostQuery($sql);

                $table->addForeignKeyConstraint(
                    $schema->getTable('oro_organization'),
                    ['organization_id'],
                    ['id'],
                    ['onDelete' => 'SET NULL', 'onUpdate' => null]
                );
            }
        }
    }
}
