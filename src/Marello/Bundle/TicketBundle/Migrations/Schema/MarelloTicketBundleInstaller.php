<?php

namespace Marello\Bundle\TicketBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class MarelloTicketBundleInstaller implements Installation
{
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
        //$this->createMarelloTicketTable($schema);

        /** Foreign keys generation **/
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
        $table->addColumn('subject', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 255]);
        $table->setPrimaryKey(['id']);
    }
}