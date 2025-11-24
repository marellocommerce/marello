<?php

namespace Marello\Bundle\TaxBundle\Migrations\Schema\v1_5;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class MarelloTaxBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->updateMarelloTaxTaxCodeTable($schema);
        $this->updateMarelloTaxTaxRateTable($schema);
        $this->updateMarelloTaxTaxRuleTable($schema);
        $this->updateMarelloTaxJurisdictionTable($schema);
        $this->updateMarelloTaxZipCodeTable($schema);
    }

    /**
     * @param Schema $schema
     * @return void
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function updateMarelloTaxTaxCodeTable(Schema $schema)
    {
        $table = $schema->getTable('marello_tax_tax_code');
        if (!$table->hasColumn('organization_id')) {
            $table->addColumn('organization_id', 'integer', ['notnull' => false]);
            $table->addForeignKeyConstraint(
                $schema->getTable('oro_organization'),
                ['organization_id'],
                ['id'],
                ['onDelete' => 'SET NULL', 'onUpdate' => null]
            );
        }

        if ($table->hasIndex('marello_tax_code_codeidx')) {
            $table->dropIndex('marello_tax_code_codeidx');
            $table->addUniqueIndex(['code', 'organization_id'], 'marello_tax_code_codeidx');
        }
    }

    /**
     * @param Schema $schema
     * @return void
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function updateMarelloTaxTaxRateTable(Schema $schema)
    {
        $table = $schema->getTable('marello_tax_tax_rate');
        if (!$table->hasColumn('organization_id')) {
            $table->addColumn('organization_id', 'integer', ['notnull' => false]);
            $table->addForeignKeyConstraint(
                $schema->getTable('oro_organization'),
                ['organization_id'],
                ['id'],
                ['onDelete' => 'SET NULL', 'onUpdate' => null]
            );
        }

        if ($table->hasIndex('marello_tax_rate_codeidx')) {
            $table->dropIndex('marello_tax_rate_codeidx');
            $table->addUniqueIndex(['code', 'organization_id'], 'marello_tax_rate_codeidx');
        }
    }

    /**
     * @param Schema $schema
     * @return void
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function updateMarelloTaxTaxRuleTable(Schema $schema)
    {
        $table = $schema->getTable('marello_tax_tax_rule');
        if (!$table->hasColumn('organization_id')) {
            $table->addColumn('organization_id', 'integer', ['notnull' => false]);
            $table->addForeignKeyConstraint(
                $schema->getTable('oro_organization'),
                ['organization_id'],
                ['id'],
                ['onDelete' => 'SET NULL', 'onUpdate' => null]
            );
        }
    }

    /**
     * @param Schema $schema
     * @return void
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function updateMarelloTaxJurisdictionTable(Schema $schema)
    {
        $table = $schema->getTable('marello_tax_tax_jurisdiction');
        if (!$table->hasColumn('organization_id')) {
            $table->addColumn('organization_id', 'integer', ['notnull' => false]);
            $table->addForeignKeyConstraint(
                $schema->getTable('oro_organization'),
                ['organization_id'],
                ['id'],
                ['onDelete' => 'SET NULL', 'onUpdate' => null]
            );
        }

        if ($table->hasIndex('marello_tax_jurisdiction_codeidx')) {
            $table->dropIndex('marello_tax_jurisdiction_codeidx');
            $table->addUniqueIndex(['code', 'organization_id'], 'marello_tax_jurisdiction_codeidx');
        }
    }

    /**
     * @param Schema $schema
     * @return void
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function updateMarelloTaxZipCodeTable(Schema $schema)
    {
        $table = $schema->getTable('marello_tax_zip_code');
        if (!$table->hasColumn('organization_id')) {
            $table->addColumn('organization_id', 'integer', ['notnull' => false]);
            $table->addIndex(['organization_id']);
            $table->addForeignKeyConstraint(
                $schema->getTable('oro_organization'),
                ['organization_id'],
                ['id'],
                ['onDelete' => 'SET NULL', 'onUpdate' => null]
            );
        }
    }
}
