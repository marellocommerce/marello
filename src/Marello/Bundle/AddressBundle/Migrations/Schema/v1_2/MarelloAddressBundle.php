<?php

namespace Marello\Bundle\AddressBundle\Migrations\Schema\v1_2;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class MarelloAddressBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->addMarelloTypedAddressTable($schema, $queries);
    }

    /**
     * Update marello_address table
     *
     * @param Schema $schema
     * @param QueryBag $queries
     */
    protected function addMarelloTypedAddressTable(Schema $schema, QueryBag $queries)
    {
        if (!$schema->hasTable('marello_typed_address')) {
            $table = $schema->createTable('marello_typed_address');
            $table->addColumn('id', 'integer', ['autoincrement' => true]);
            $table->addColumn('address_type', 'string', ['notnull' => false]);
            $table->addColumn('country_code', 'string', ['notnull' => false, 'length' => 2]);
            $table->addColumn('region_code', 'string', ['notnull' => false, 'length' => 16]);
            $table->addColumn('phone', 'string', ['notnull' => false, 'length' => 32]);
            $table->addColumn('label', 'string', ['notnull' => false, 'length' => 255]);
            $table->addColumn('street', 'string', ['notnull' => false, 'length' => 500]);
            $table->addColumn('street2', 'string', ['notnull' => false, 'length' => 500]);
            $table->addColumn('city', 'string', ['notnull' => false, 'length' => 255]);
            $table->addColumn('postal_code', 'string', ['notnull' => false, 'length' => 255]);
            $table->addColumn('company', 'string', ['notnull' => false, 'length' => 255]);
            $table->addColumn('organization', 'string', ['notnull' => false, 'length' => 255]);
            $table->addColumn('region_text', 'string', ['notnull' => false, 'length' => 255]);
            $table->addColumn('name_prefix', 'string', ['notnull' => false, 'length' => 255]);
            $table->addColumn('first_name', 'string', ['notnull' => false, 'length' => 255]);
            $table->addColumn('middle_name', 'string', ['notnull' => false, 'length' => 255]);
            $table->addColumn('last_name', 'string', ['notnull' => false, 'length' => 255]);
            $table->addColumn('name_suffix', 'string', ['notnull' => false, 'length' => 255]);
            $table->addColumn('is_default', 'boolean', ['default' => '0']);
            $table->addColumn('created', 'datetime', []);
            $table->addColumn('updated', 'datetime', []);
            $table->setPrimaryKey(['id']);
            $table->addIndex(['address_type']);

            $table->addForeignKeyConstraint(
                $schema->getTable('oro_address_type'),
                ['address_type'],
                ['name'],
                ['onDelete' => 'SET NULL', 'onUpdate' => null]
            );
            $table->addForeignKeyConstraint(
                $schema->getTable('oro_dictionary_country'),
                ['country_code'],
                ['iso2_code'],
                ['onDelete' => null, 'onUpdate' => null]
            );
            $table->addForeignKeyConstraint(
                $schema->getTable('oro_dictionary_region'),
                ['region_code'],
                ['combined_code'],
                ['onDelete' => null, 'onUpdate' => null]
            );
        }
    }
}
