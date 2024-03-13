<?php

namespace Marello\Bundle\TicketBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class MarelloTicketBundleInstaller implements Installation, ExtendExtensionAwareInterface
{
    /**
     * @var ExtendExtension
     */
    protected $extendExtension;

    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_0';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createTicketCategoryTypeTable($schema);
        $this->createMarelloTicketTable($schema);

        /** Foreign keys generation **/
        $this->addMarelloTicketForeignKeys($schema);
    }

    /**
     * Create ticket_category_type table
     *
     * @param Schema $schema
     */
    protected function createTicketCategoryTypeTable(Schema $schema)
    {
        $table = $schema->createTable('ticket_category_type');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('category', 'string', ['length' => 255]);
        $table->addColumn('serialized_data', 'json', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
    }

    /**
     * Create marello_ticket table
     *
     * @param Schema $schema
     */
    protected function createMarelloTicketTable(Schema $schema)
    {
        $table = $schema->createTable('marello_ticket');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('customer_id', 'integer', ['notnull' => false]);
        $table->addColumn('owner_id', 'integer', []);
        $table->addColumn('assigned_to_id', 'integer', ['notnull' => false]);
        $table->addColumn('category_id', 'integer', []);
        $table->addColumn('subject', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 1000]);
        $table->addColumn('firstname', 'string', ['length' => 255]);
        $table->addColumn('lastname', 'string', ['length' => 255]);
        $table->addColumn('email', 'string', ['length' => 255]);
        $table->addColumn('phone', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('resolution', 'string', ['notnull' => false, 'length' => 1000]);
        $table->addColumn('serialized_data', 'json', ['notnull' => false]);
        $table->addIndex(['owner_id'], 'idx_5bb311787e3c61f9', []);
        $table->addIndex(['assigned_to_id'], 'idx_5bb31178f4bd7827', []);
        $table->addIndex(['category_id'], 'idx_5bb3117812469de2', []);
        $table->addIndex(['customer_id'], 'idx_5bb311789395c3f3', []);
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
        $table->setPrimaryKey(['id']);
    }

    /**
     * Add marello_ticket foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloTicketForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_ticket');
        $table->addForeignKeyConstraint(
            $schema->getTable('ticket_category_type'),
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