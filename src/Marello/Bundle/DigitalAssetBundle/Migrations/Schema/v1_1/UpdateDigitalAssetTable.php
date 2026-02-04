<?php

namespace Marello\Bundle\DigitalAssetBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;
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
        /** Add additional fields/relations */
        $this->addProductAssetRelation($schema);
    }

    protected function addProductAssetRelation(Schema $schema)
    {
        $this->extendExtension->addManyToManyRelation(
            $schema,
            'oro_digital_asset',
            'assetProducts',
            'marello_product_product',
            ['sku'], // column names are used to show a title of related entity
            ['name'], // column names are used to show detailed info about related entity
            ['sku'], // Column names are used to show related entity in a grid
            [
                'extend' => [
                    'owner' => ExtendScope::OWNER_CUSTOM
                ],
                'entity' => ['label' => 'Assets Products Relation'],
            ]
        );
    }
}