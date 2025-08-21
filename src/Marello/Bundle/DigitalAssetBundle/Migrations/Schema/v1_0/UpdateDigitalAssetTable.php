<?php

namespace Marello\Bundle\DigitalAssetBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;
use Marello\Bundle\DigitalAssetBundle\Form\Type\DigitalAssetCategorySelectType;
use Oro\Bundle\DigitalAssetBundle\Entity\DigitalAsset;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigFieldValueQuery;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;

class UpdateDigitalAssetTable implements Migration, ExtendExtensionAwareInterface
{
    /**
     * @var ExtendExtension
     */
    private $extendExtension;

    /**
     * @param ExtendExtension $extendExtension
     */
    public function setExtendExtension(ExtendExtension $extendExtension)
    {
        $this->extendExtension = $extendExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createDigitalAssetCategoryTable($schema);

        /** Add additional field */
        $this->addVersionField($schema);

        /** Foreign keys generation **/
        $this->addDigitalAssetForeignKeys($schema);

        $queries->addQuery(
            new UpdateEntityConfigFieldValueQuery(
                DigitalAsset::class,
                'marello_digital_asset_category_rel',
                'form',
                'form_type',
                DigitalAssetCategorySelectType::class
            )
        );
    }

    protected function createDigitalAssetCategoryTable(Schema $schema)
    {
        $table = $schema->createTable('marello_digital_asset_category');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
    }

    protected function addVersionField(Schema $schema)
    {
        $table = $schema->getTable('oro_digital_asset');
        if (!$table->hasColumn('version')) {
            $table->addColumn('version', 'string', [
                'oro_options' => [
                    'extend' => [
                        'is_extend' => true,
                        'owner' => ExtendScope::OWNER_CUSTOM,
                        'nullable' => true,
                        'on_delete' => 'SET NULL'
                    ],
                ],
                [
                    'notnull' => false
                ]
            ]);
        }
    }

    protected function addDigitalAssetForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('oro_digital_asset');
        $targetTable = $schema->getTable('marello_digital_asset_category');

        $this->extendExtension->addManyToOneRelation(
            $schema,
            $table,
            'marello_digital_asset_category_rel',
            $targetTable,
            'name',
            [
                'entity' => ['label' => 'marello.digitalasset.digitalassetcategory.entity_label'],
                'extend' => ['owner' => ExtendScope::OWNER_CUSTOM],
                'form' => ['is_enabled' => true],
                'view' => ['is_displayable' => true],
                'dataaudit' => ['auditable' => true]
            ]
        );
    }
}