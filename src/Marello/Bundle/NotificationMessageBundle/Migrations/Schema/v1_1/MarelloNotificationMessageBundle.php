<?php

namespace Marello\Bundle\NotificationMessageBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class MarelloNotificationMessageBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateNotificationMessageTable($schema);
    }

    protected function updateNotificationMessageTable(Schema $schema)
    {
        $table = $schema->getTable('marello_notification_message');
        if ($table->hasColumn('title')) {
            $table->changeColumn('title',  ['length' => 64]);
        }
    }
}
