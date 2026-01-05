<?php

namespace marello\src\Marello\Bundle\CustomerBundle\Migrations\Schema\v1_7_5;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\ParametrizedSqlMigrationQuery;

use Marello\Bundle\CustomerBundle\Migrations\Schema\MarelloCustomerBundleInstaller;

class MarelloCustomerBundle implements Migration
{
    /**
     * @inheritDoc
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $queries->addPreQuery(
            new ParametrizedSqlMigrationQuery(
                'INSERT INTO marello_typed_address (
                    id,
                    country_code,
                    region_code,
                    phone,
                    label,
                    street,
                    street2,
                    city,
                    postal_code,
                    company,
                    organization,
                    region_text,
                    name_prefix,
                    first_name,
                    middle_name,
                    last_name,
                    name_suffix,
                    created,
                    updated
                )
                SELECT
                    id,
                    country_code,
                    region_code,
                    phone,
                    label,
                    street,
                    street2,
                    city,
                    postal_code,
                    company,
                    organization,
                    region_text,
                    name_prefix,
                    first_name,
                    middle_name,
                    last_name,
                    name_suffix,
                    created,
                    updated
                FROM marello_address WHERE id IN (SELECT address_id FROM marello_company_join_address)'
            )
        );
        $this->updateMarelloCompanyAddressForeignKeys($schema);
    }

    protected function updateMarelloCompanyAddressForeignKeys(Schema $schema) {
        $table = $schema->getTable(MarelloCustomerBundleInstaller::MARELLO_COMPANY_JOIN_ADDRESS_TABLE);

        if ($table->hasForeignKey('fk_27fa21c5f5b7af75')) {
            $table->removeForeignKey('fk_27fa21c5f5b7af75');
            $table->addForeignKeyConstraint(
                $schema->getTable('marello_typed_address'),
                ['address_id'],
                ['id'],
                ['onDelete' => null, 'onUpdate' => null]
            );
        }
    }
}