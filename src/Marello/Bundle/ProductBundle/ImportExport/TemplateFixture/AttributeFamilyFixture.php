<?php

namespace Marello\Bundle\ProductBundle\ImportExport\TemplateFixture;

use Doctrine\ORM\EntityManagerInterface;
use Marello\Bundle\ProductBundle\Entity\Builder\ProductFamilyBuilder;
use Oro\Bundle\EntityConfigBundle\Attribute\Entity\AttributeFamily;
use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;

class AttributeFamilyFixture extends AbstractTemplateRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    protected function createEntity($key)
    {
        $attributeFamily = $this->entityManager
            ->getRepository(AttributeFamily::class)
            ->findOneBy(['code' => ProductFamilyBuilder::DEFAULT_FAMILY_CODE]);

        if (!$attributeFamily) {
            $attributeFamily = new AttributeFamily();
            $attributeFamily->setCode(ProductFamilyBuilder::DEFAULT_FAMILY_CODE);
        }

        return $attributeFamily;
    }

    public function getEntityClass()
    {
        return AttributeFamily::class;
    }

    public function getData()
    {
        return $this->getEntityData('default');
    }

    public function fillEntityData($key, $entity)
    {
        switch ($key) {
            case 'default':
                return;
        }

        parent::fillEntityData($key, $entity);
    }
}
