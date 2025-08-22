<?php

namespace Marello\Bundle\CatalogBundle\Migrations\Schema\v1_3;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class MarelloCatalogBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Add additional fields */
        $this->updateMarelloCatalogCategoryTable($schema);

        /** Tables generation **/
        $this->createMarelloCatalogCategoryCompaniesTable($schema);

        /** Foreign keys generation **/
        $this->addCatalogCategoryCompaniesForeignKeys($schema);
        $this->addCatalogCategoryForeignKeys($schema);
    }

    /**
     * @param Schema $schema
     */
    protected function updateMarelloCatalogCategoryTable(Schema $schema)
    {
        $table = $schema->getTable('marello_catalog_category');
        if (!$table->hasColumn('type')) {
            $table->addColumn('type', 'string', ['notnull' => false]);
        }

        if (!$table->hasColumn('customer_id')) {
            $table->addColumn('customer_id', 'integer', ['notnull' => false]);
        }

        if (!$table->hasColumn('is_personal')) {
            $table->addColumn('is_personal', 'boolean', ['notnull' => false]);
        }
    }

    /**
     * @param Schema $schema
     */
    protected function createMarelloCatalogCategoryCompaniesTable(Schema $schema)
    {
        $table = $schema->createTable('marello_category_company');
        $table->addColumn('category_id', 'integer', []);
        $table->addColumn('company_id', 'integer', []);
        $table->setPrimaryKey(['category_id', 'company_id']);
    }

    /**
     * @param Schema $schema
     */
    protected function addCatalogCategoryCompaniesForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_category_company');
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_catalog_category'),
            ['category_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_customer_company'),
            ['company_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * @param Schema $schema
     */
    protected function addCatalogCategoryForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_catalog_category');

        if (!$table->hasForeignKey('fk_marello_catalog_category_customer')) {
            $table->addForeignKeyConstraint(
                $schema->getTable('marello_customer_customer'),
                ['customer_id'],
                ['id'],
                ['onDelete' => 'SET NULL', 'onUpdate' => null]
            );
        }
    }
}