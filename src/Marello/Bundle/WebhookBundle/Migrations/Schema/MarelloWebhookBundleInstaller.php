<?php

namespace Marello\Bundle\WebhookBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class MarelloWebhookBundleInstaller implements Installation, ExtendExtensionAwareInterface
{
    protected ExtendExtension $extendExtension;

    public function setExtendExtension(ExtendExtension $extendExtension)
    {
        $this->extendExtension = $extendExtension;
    }

    public function getMigrationVersion()
    {
        return 'v1_1';
    }

    public function up(Schema $schema, QueryBag $queries)
    {
        $this->addMarelloWebhookTable($schema);
        $this->addMarelloWebhookForeignKeys($schema);
    }

    protected function addMarelloWebhookTable(Schema $schema)
    {
        $table = $schema->createTable('marello_webhook');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('name', 'string');
        $table->addColumn('event', 'string', ['notnull' => false]);
        $table->addColumn('callback_url', 'string');
        $table->addColumn('secret', 'string');
        $table->addColumn('enabled', 'boolean');
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->setPrimaryKey(['id']);
        $table->addIndex(['organization_id']);
        $table->addIndex(['created_at'], 'idx_marello_webhook_created_at', []);
        $table->addIndex(['updated_at'], 'idx_marello_webhook_updated_at', []);
    }

    protected function addMarelloWebhookForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_webhook');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }
}
