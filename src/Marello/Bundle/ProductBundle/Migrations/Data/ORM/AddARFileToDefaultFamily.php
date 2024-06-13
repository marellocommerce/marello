<?php

namespace Marello\Bundle\ProductBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Oro\Bundle\EntityConfigBundle\Attribute\Entity\AttributeFamily;
use Oro\Bundle\EntityConfigBundle\Attribute\Entity\AttributeGroupRelation;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\Entity\Builder\ProductFamilyBuilder;

class AddARFileToDefaultFamily extends AbstractFixture implements
    ContainerAwareInterface,
    DependentFixtureInterface
{
    use MakeProductAttributesTrait;

    public function load(ObjectManager $manager)
    {
        $attributeFamilyRepository = $manager->getRepository(AttributeFamily::class);
        $attributeFamilies =
            $attributeFamilyRepository->findBy([
                'code' => [
                    ProductFamilyBuilder::DEFAULT_FAMILY_CODE,
                ]
            ]);
        $configManager = $this->getConfigManager();

        /** @var AttributeFamily $attributeFamily */
        foreach ($attributeFamilies as $attributeFamily) {
            $defaultAttributeGroup = $attributeFamily->getAttributeGroup(ProductFamilyBuilder::DEFAULT_ATTRIBUTE_GROUP);
            $fieldConfigModel = $configManager->getConfigFieldModel(Product::class, 'ARFile');
            $attributeGroupRelation = new AttributeGroupRelation();
            $attributeGroupRelation->setEntityConfigFieldId($fieldConfigModel->getId());
            $defaultAttributeGroup->addAttributeRelation($attributeGroupRelation);

            $manager->persist($defaultAttributeGroup);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            LoadDefaultProductFamilyData::class
        ];
    }
}
