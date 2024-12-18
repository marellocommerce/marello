<?php

namespace Marello\Bundle\WebhookBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class MarelloWebhookBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->addMarelloWebhookTable($schema);
    }

    protected function addMarelloWebhookTable(Schema $schema)
    {
        $table = $schema->createTable('marello_webhook');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('name', 'string', ['notnull' => false]);
        $table->addColumn('event', 'string', ['notnull' => false]);
        $table->addColumn('callback_url', 'string', ['notnull' => false]);
        $table->addColumn('secret', 'string', ['notnull' => false]);
        $table->addColumn('enabled', 'boolean', ['notnull' => false, 'default' => true]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['organization_id']);
    }
}
