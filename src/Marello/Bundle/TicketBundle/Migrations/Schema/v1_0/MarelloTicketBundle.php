<?php

namespace Marello\Bundle\TicketBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;

class MarelloTicketBundle implements Migration, ExtendExtensionAwareInterface
{
    /**
     * @var ExtendExtension
     */
    protected $extendExtension;

    public function up(Schema $schema, QueryBag $queries)
    {
        $this->createTicketCategoryTable($schema);
        $this->createMarelloTicketTable($schema);

        $this->addMarelloTicketForeignKeys($schema);
    }

    protected function createTicketCategoryTable(Schema $schema)
    {
        $table = $schema->createTable('marello_ticket_ticket_category');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
    }

    protected function createMarelloTicketTable(Schema $schema)
    {
        $table = $schema->createTable('marello_ticket_ticket');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('customer_id', 'integer', ['notnull' => false]);
        $table->addColumn('owner_id', 'integer', []);
        $table->addColumn('assigned_to_id', 'integer', ['notnull' => false]);
        $table->addColumn('category_id', 'integer', []);
        $table->addColumn('subject', 'string', ['length' => 255]);
        $table->addColumn('description', 'text', ['comment' => '(DC2Type:text)']);
        $table->addColumn('name_prefix', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('first_name', 'string', ['length' => 255]);
        $table->addColumn('middle_name', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('last_name', 'string', ['length' => 255]);
        $table->addColumn('name_suffix', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('email', 'string', ['length' => 255]);
        $table->addColumn('phone', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('resolution', 'text', ['notnull' => false, 'comment' => '(DC2Type:text)']);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime', ['notnull' => false]);
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
        $this->extendExtension->addEnumField(
            $schema,
            $table,
            'ticketSource',
            'marello_ticket_source',
            false,
            false,
            [
                'extend' => ['owner' => ExtendScope::OWNER_SYSTEM],
            ]
        );
        $this->extendExtension->addEnumField(
            $schema,
            $table,
            'ticketStatus',
            'marello_ticket_status',
            false,
            false,
            [
                'extend' => ['owner' => ExtendScope::OWNER_SYSTEM],
            ]
        );
        $table->setPrimaryKey(['id']);
    }

    protected function addMarelloTicketForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_ticket_ticket');
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_ticket_ticket_category'),
            ['category_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['owner_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_customer_customer'),
            ['customer_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['assigned_to_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
    }

    public function setExtendExtension(ExtendExtension $extendExtension)
    {
        $this->extendExtension = $extendExtension;
    }
}
