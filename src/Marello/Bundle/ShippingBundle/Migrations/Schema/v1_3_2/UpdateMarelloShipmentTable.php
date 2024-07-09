<?php

namespace Marello\Bundle\ShippingBundle\Migrations\Schema\v1_3_2;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class UpdateMarelloShipmentTable implements Migration
{
    /**
     * @param Schema $schema
     * @param QueryBag $queries
     * @return void
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateMarelloShipmentTable($schema);
    }

    /**
     * @param Schema $schema
     * @return void
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function updateMarelloShipmentTable(Schema $schema)
    {
        $table = $schema->getTable('marello_shipment');
        if ($table->hasForeignKey('FK_A2D00FBC224940C0')) {
            $table->removeForeignKey('FK_A2D00FBC224940C0');
            $table->addForeignKeyConstraint(
                $schema->getTable('marello_tracking_info'),
                ['tracking_info_id'],
                ['id'],
                ['onDelete' => null, 'onUpdate' => null]
            );
        }
    }
}
