<?php

namespace Marello\Bundle\TicketBundle\Migrations\Schema\v1_2;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class MarelloTicketBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateTicketTable($schema);
        $this->updateTicketTableForeignKeys($schema);
    }

    protected function updateTicketTable(Schema $schema)
    {
        $table = $schema->getTable('marello_ticket_ticket');
        if (!$table->hasColumn('organization_id')) {
            $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        }
    }

    protected function updateTicketTableForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_ticket_ticket');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }
}
