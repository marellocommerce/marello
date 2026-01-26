<?php

namespace Marello\Bundle\CatalogBundle\Migrations\Schema\v1_3;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Marello\Bundle\CatalogBundle\Entity\Category;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\ParametrizedSqlMigrationQuery;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddLocalizedFieldsToCategory implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->createMarelloCatalogCategoryNameTable($schema);
        $this->createMarelloCatalogCategoryDescTable($schema);
        $this->addMarelloCatalogCategoryNameForeignKeys($schema);
        $this->addMarelloCatalogCategoryDescForeignKeys($schema);

        // Remove old field configuration from entity config
        $dropFields = ['name', 'description'];
        $dropFieldInConfigSql = <<<EOF
DELETE FROM oro_entity_config_field
WHERE field_name = :field_name
AND entity_id IN (SELECT id FROM oro_entity_config WHERE class_name = :class_name)
EOF;
        foreach ($dropFields as $field) {
            $dropFieldInConfigQuery = new ParametrizedSqlMigrationQuery();
            $dropFieldInConfigQuery->addSql(
                $dropFieldInConfigSql,
                ['field_name' => $field, 'class_name' => Category::class],
                ['field_name' => Type::STRING, 'class_name' => Type::STRING]
            );
            $queries->addPostQuery($dropFieldInConfigQuery);
        }
    }

    /**
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
