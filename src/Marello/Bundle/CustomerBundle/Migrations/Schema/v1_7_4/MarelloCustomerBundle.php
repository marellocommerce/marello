<?php

namespace Marello\Bundle\CustomerBundle\Migrations\Schema\v1_7_4;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class MarelloCustomerBundle implements Migration
{
    /**
     * @inheritDoc
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateMarelloCustomerTable($schema);
    }

    /**
     * Create marello_customer_customer table
     */
    protected function updateMarelloCustomerTable(Schema $schema): void
    {
        $table = $schema->getTable('marello_customer_customer');
        if (!$table->hasColumn('data')) {
            $table->addColumn('data', 'json', ['notnull' => false, 'comment' => '(DC2Type:json)']);
        }
    }
}
