<?php

namespace Marello\Bundle\CatalogBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class MarelloCatalogBundleInstaller implements Installation, ActivityExtensionAwareInterface
{
    /**
     * @var ActivityExtension
     */
    protected $activityExtension;

    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_4';
    }

    /**
     * {@inheritdoc}
     */
    public function setActivityExtension(ActivityExtension $activityExtension)
    {
        $this->activityExtension = $activityExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createCatalogCategoryTable($schema);
        $this->createCategoryToProductTable($schema);
        $this->createMarelloCatalogCategoryNameTable($schema);
        $this->createMarelloCatalogCategoryDescTable($schema);

        /** Foreign keys generation **/
        $this->addCatalogCategoryForeignKeys($schema);
        $this->addCategoryToProductForeignKeys($schema);
        $this->addMarelloCatalogCategoryNameForeignKeys($schema);
        $this->addMarelloCatalogCategoryDescForeignKeys($schema);
    }

    /**
     * Create marello_catalog_category table
     *
     * @param Schema $schema
     */
    protected function createCatalogCategoryTable(Schema $schema)
    {
        $table = $schema->createTable('marello_catalog_category');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('code', 'string', ['length' => 255]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['code', 'organization_id'], 'marello_catalog_category_codeorgidx');

        $this->activityExtension->addActivityAssociation($schema, 'oro_note', 'marello_catalog_category');
    }

    /**
     * Create marello_category_to_product table
     *
     * @param Schema $schema
     */
    protected function createCategoryToProductTable(Schema $schema)
    {
        $table = $schema->createTable('marello_category_to_product');
        $table->addColumn('category_id', 'integer', []);
        $table->addColumn('product_id', 'integer', []);
        $table->setPrimaryKey(['category_id', 'product_id']);
    }

    /**
     * Add marello_catalog_category foreign keys.
     *
     * @param Schema $schema
     */
    protected function addCatalogCategoryForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_catalog_category');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }

    /**
     * Add marello_category_to_product foreign keys.
     *
     * @param Schema $schema
     */
    protected function addCategoryToProductForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_category_to_product');
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_catalog_category'),
            ['category_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_product_product'),
            ['product_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * Create marello_catalog_category_name table
     *
     * @param Schema $schema
     */
    protected function createMarelloCatalogCategoryNameTable(Schema $schema)
    {
        $table = $schema->createTable('marello_catalog_category_name');
        $table->addColumn('category_id', 'integer', []);
        $table->addColumn('localized_value_id', 'integer', []);
        $table->setPrimaryKey(['category_id', 'localized_value_id']);
        $table->addUniqueIndex(['localized_value_id'], 'uniq_marello_cat_cat_name_loc_val_id');
    }

    /**
     * Create marello_catalog_category_desc table
     *
     * @param Schema $schema
     */
    protected function createMarelloCatalogCategoryDescTable(Schema $schema)
    {
        $table = $schema->createTable('marello_catalog_category_desc');
        $table->addColumn('category_id', 'integer', []);
        $table->addColumn('localized_value_id', 'integer', []);
        $table->setPrimaryKey(['category_id', 'localized_value_id']);
        $table->addUniqueIndex(['localized_value_id'], 'uniq_marello_cat_cat_desc_loc_val_id');
    }

    /**
     * Add marello_catalog_category_name foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloCatalogCategoryNameForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_catalog_category_name');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_catalog_category'),
            ['category_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * Add marello_catalog_category_desc foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloCatalogCategoryDescForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_catalog_category_desc');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_catalog_category'),
            ['category_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }
}
