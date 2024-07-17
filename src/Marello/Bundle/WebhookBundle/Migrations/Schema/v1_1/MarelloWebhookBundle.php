<?php

namespace Marello\Bundle\WebhookBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class MarelloWebhookBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->addMarelloWebhookForeignKeys($schema);
        $this->updateMarelloWebhookIndex($schema);
    }

    protected function updateMarelloWebhookIndex(Schema $schema)
    {
        $table = $schema->getTable('marello_webhook');
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
