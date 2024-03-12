<?php

namespace Marello\Bundle\TicketBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class UpdateTicketPriority implements Migration, ExtendExtensionAwareInterface
{
    /**
     * @var ExtendExtension
     */
    protected $extendExtension;

    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateTicketPriority($schema);
    }

    protected function updateTicketPriority(Schema $schema)
    {
        $table = $schema->getTable('marello_ticket');

        $tableName = $this->extendExtension->getNameGenerator()->generateEnumTableName('marello_ticket_priority');
        if ($schema->hasTable($tableName)) {
            return;
        }

        $this->extendExtension->addEnumField(
            $schema,
            $table,
            'ticketPriority',
            'marello_ticket_priority',
            false,
            false,
            [
                'extend' => ['owner' => ExtendScope::OWNER_SYSTEM],
            ]
        );
    }

    public function setExtendExtension(ExtendExtension $extendExtension)
    {
        $this->extendExtension = $extendExtension;
    }
}