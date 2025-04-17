<?php

namespace Marello\Bundle\ProductBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class UpdateAttributesFrontendConfig extends AbstractFixture implements
    DependentFixtureInterface,
    ContainerAwareInterface
{
    use MakeProductAttributesTrait;

    /**
     * @var array
     */
    const ATTRIBUTES = [
        'sku' => [
            'is_displayable' => false,
            'is_editable' => false
        ],
        'names' => [
            'is_displayable' => false,
            'is_editable' => false
        ],
        'channels' => [
            'is_displayable' => false,
            'is_editable' => false
        ],
        'status' => [
            'is_displayable' => false,
            'is_editable' => false
        ],
        'prices' => [
            'is_displayable' => false,
            'is_editable' => false
        ],
        'channelPrices' => [
            'is_displayable' => false,
            'is_editable' => false
        ],
        'taxCode' => [
            'is_displayable' => false,
            'is_editable' => false
        ],
        'salesChannelTaxCodes' => [
            'is_displayable' => false,
            'is_editable' => false
        ],
        'weight' => [
            'is_displayable' => true,
            'is_editable' => false
        ],
        'manufacturingCode' => [
            'is_displayable' => true,
            'is_editable' => false
        ],
        'barcode' => [
            'is_displayable' => true,
            'is_editable' => false
        ],
        'warranty' => [
            'is_displayable' => true,
            'is_editable' => false
        ],
        'suppliers' => [
            'is_displayable' => false,
            'is_editable' => false
        ],
        'categories' => [
            'is_displayable' => false,
            'is_editable' => false
        ],
        'image' => [
            'is_displayable' => false,
            'is_editable' => false
        ],
        'ARFile' => [
            'is_displayable' => false,
            'is_editable' => false
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->updateProductAttributes(self::ATTRIBUTES, 'frontend');
    }

    /**
     * @inheritdoc
     */
    public function getDependencies()
    {
        return [
            LoadDefaultProductFamilyData::class
        ];
    }
}
