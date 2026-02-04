<?php

namespace Marello\Bundle\TicketBundle\Migrations\Schema\v1_2_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class MarelloTicketBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateTicketTable($schema);
    }

    protected function updateTicketTable(Schema $schema)
    {
        $table = $schema->getTable('marello_ticket_ticket');
        if ($table->hasColumn('owner_id')) {
            $table->changeColumn('owner_id', ['notnull' => false]);
        }
    }
}
