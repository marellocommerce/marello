<?php

namespace Marello\Bundle\ProductBundle\Migrations\Schema\v1_17;

use Doctrine\DBAL\Schema\Schema;

use Marello\Bundle\ProductBundle\Migrations\Schema\MarelloProductBundleInstaller;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtensionAwareTrait;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtensionAwareInterface;
class UpdateVariantTables implements Migration, AttachmentExtensionAwareInterface
{
    use AttachmentExtensionAwareTrait;

    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('marello_product_variant');
        if (!$table->hasColumn('name')) {
            $table->addColumn('name', 'string', ['length' => 255, 'notnull' => false]);
        }

        if (!$table->hasColumn('variant_fields')) {
            $table->addColumn('variant_fields', 'array', ['notnull' => false, 'comment' => '(DC2Type:array)']);
        }

        $this->createVariantProductNameTable($schema);
        $this->createVariantProductDescriptionTable($schema);

        $this->addVariantProductNameForeignKeys($schema);
        $this->addVariantProductDescriptionForeignKeys($schema);

        $this->addMediaAttributesToVariants($schema);
    }

    /**
     * @param Schema $schema
     */
    protected function createVariantProductNameTable(Schema $schema)
    {
        if (!$schema->hasTable('marello_product_variant_name')) {
            $table = $schema->createTable('marello_product_variant_name');
            $table->addColumn('variant_id', 'integer', []);
            $table->addColumn('localized_value_id', 'integer', []);
            $table->setPrimaryKey(['variant_id', 'localized_value_id']);
            $table->addUniqueIndex(['localized_value_id']);
        }
    }

    /**
     * @param Schema $schema
     */
    protected function createVariantProductDescriptionTable(Schema $schema)
    {
        if (!$schema->hasTable('marello_product_variant_desc')) {
            $table = $schema->createTable('marello_product_variant_desc');
            $table->addColumn('variant_id', 'integer', []);
            $table->addColumn('localized_value_id', 'integer', []);
            $table->setPrimaryKey(['variant_id', 'localized_value_id']);
            $table->addUniqueIndex(['localized_value_id']);
        }
    }

    /**
     * @param Schema $schema
     */
    protected function addVariantProductNameForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_product_variant_name');
        if (!$table->hasForeignKey('fk_3aa32512eb576e89') && !$table->hasForeignKey('fk_3aa325123b69a9af')) {
            $table->addForeignKeyConstraint(
                $schema->getTable('oro_fallback_localization_val'),
                ['localized_value_id'],
                ['id'],
                ['onUpdate' => null, 'onDelete' => 'CASCADE']
            );
            $table->addForeignKeyConstraint(
                $schema->getTable('marello_product_variant'),
            ['variant_id'],
                ['id'],
                ['onUpdate' => null, 'onDelete' => 'CASCADE']
            );
        }
    }

    /**
     * @param Schema $schema
     */
    protected function addVariantProductDescriptionForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_product_variant_desc');
        if (!$table->hasForeignKey('fk_6f35f740eb576e89') && !$table->hasForeignKey('fk_6f35f7403b69a9af')) {
            $table->addForeignKeyConstraint(
                $schema->getTable('oro_fallback_localization_val'),
                ['localized_value_id'],
                ['id'],
                ['onUpdate' => null, 'onDelete' => 'CASCADE']
            );
            $table->addForeignKeyConstraint(
                $schema->getTable('marello_product_variant'),
                ['variant_id'],
                ['id'],
                ['onUpdate' => null, 'onDelete' => 'CASCADE']
            );
        }
    }

    protected function addMediaAttributesToVariants(Schema $schema)
    {
        if (!$schema->getTable('marello_product_variant')->hasColumn('image_id')) {
            // add image and ARFile to variants
            $this->attachmentExtension->addImageRelation(
                $schema,
                'marello_product_variant',
                'image',
                [
                    'importexport' => ['excluded' => true],
                    'extend' => ['owner' => ExtendScope::OWNER_SYSTEM],
                    'attachment' => [
                        'acl_protected' => false
                    ]
                ],
                MarelloProductBundleInstaller::MAX_PRODUCT_IMAGE_SIZE_IN_MB,
                MarelloProductBundleInstaller::MAX_PRODUCT_IMAGE_DIMENSIONS_IN_PIXELS,
                MarelloProductBundleInstaller::MAX_PRODUCT_IMAGE_DIMENSIONS_IN_PIXELS
            );
        }

        if (!$schema->getTable('marello_product_variant')->hasColumn('arfile_id')) {
            $this->attachmentExtension->addFileRelation(
                $schema,
                'marello_product_variant',
                'ARFile',
                [
                    'importexport' => ['excluded' => true],
                    'extend' => ['owner' => ExtendScope::OWNER_SYSTEM],
                    'attachment' => ['mimetypes' => 'application/zip,model/vnd.usdz+zip', 'acl_protected' => false]
                ],
                MarelloProductBundleInstaller::MAX_PRODUCT_ARFILE_SIZE_IN_MB
            );
        }
    }
}
