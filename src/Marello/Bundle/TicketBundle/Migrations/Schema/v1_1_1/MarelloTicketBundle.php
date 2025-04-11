<?php

namespace Marello\Bundle\TicketBundle\Migrations\Schema\v1_1_1;

use Doctrine\DBAL\Schema\Schema;

use Marello\Bundle\TicketBundle\Migrations\Schema\MarelloTicketBundleInstaller;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtension;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtensionAwareInterface;

class MarelloTicketBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateTicketTable($schema);
    }

    protected function updateTicketTable(Schema $schema)
    {
        $table = $schema->getTable('marello_ticket_ticket');
        if (!$table->hasColumn('company')) {
            $table->addColumn('company', 'string', ['notnull' => false]);
        }
    }
}
