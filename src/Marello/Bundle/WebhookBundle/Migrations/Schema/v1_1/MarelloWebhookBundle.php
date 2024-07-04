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
