<?php

namespace Marello\Bundle\ProductBundle\EventListener;

use Symfony\Contracts\Translation\TranslatorInterface;

use Oro\Bundle\UIBundle\View\ScrollData;
use Oro\Bundle\SecurityBundle\Form\FieldAclHelper;
use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;
use Oro\Bundle\EntityConfigBundle\Entity\FieldConfigModel;
use Oro\Bundle\EntityConfigBundle\Provider\ConfigProvider;
use Oro\Bundle\EntityConfigBundle\Manager\AttributeManager;
use Oro\Bundle\EntityConfigBundle\Attribute\Entity\AttributeGroup;
use Oro\Bundle\EntityConfigBundle\Attribute\Entity\AttributeFamilyAwareInterface;
use Oro\Bundle\EntityConfigBundle\EventListener\AttributeFormViewListener as BaseAttributeFormViewListener;

use Marello\Bundle\ProductBundle\Entity\Product;

class AttributeFormViewListener extends BaseAttributeFormViewListener
{
    private const DEFAULT_PRIORITY = 500;
    private const EVENT_TYPE_VIEW = 'view';

    /**
     * @var array
     */
    private $fieldsRestrictedToMove = [
        'sku',
        'names',
        'channels',
        'status',
        'prices',
        'channelPrices',
        'taxCode',
        'salesChannelTaxCodes',
        'weight',
        'manufacturingCode',
        'warranty',
        'suppliers',
        'categories',
        'image',
        'ARFile',
        'barcode'
    ];

    /**
     * This property used to determine type of event inside moveFieldToBlock.
     * It's safe because it wll be cleared after event processing
     *
     * @var string
     */
    private $eventType;

    public function __construct(
        private AttributeManager $attributeManager,
        private FieldAclHelper $fieldAclHelper,
        private ConfigProvider $entityConfigProvider,
        private TranslatorInterface $translator,
    ) {
        parent::__construct($attributeManager, $fieldAclHelper);
    }

    /**
     * {@inheritDoc}
     */
    public function onViewList(BeforeListRenderEvent $event)
    {
        $this->eventType = self::EVENT_TYPE_VIEW;

        $entity = $event->getEntity();

        if (!$entity instanceof AttributeFamilyAwareInterface) {
            return;
        }

        $groups = $this->attributeManager->getGroupsWithAttributes($entity->getAttributeFamily());
        $scrollData = $event->getScrollData();
        $this->filterGroupAttributes($groups, 'view', 'is_displayable');
        $this->addNotEmptyGroupBlocks($scrollData, $groups);

        /** @var AttributeGroup $group */
        foreach ($groups as $groupData) {
            /** @var AttributeGroup $group */
            $group = $groupData['group'];

            /** @var FieldConfigModel $attribute */
            foreach ($groupData['attributes'] as $attribute) {
                $fieldName = $attribute->getFieldName();
                if (in_array($fieldName, $this->getRestrictedToMoveFields(), true)) {
                    continue;
                }
                if ($scrollData->hasNamedField($fieldName)) {
                    $this->moveFieldToBlock($scrollData, $fieldName, $group->getCode());
                    continue;
                }

                $html = $event->getEnvironment()->render(
                    '@OroEntityConfig/Attribute/attributeView.html.twig',
                    [
                        'entity' => $entity,
                        'field' => $attribute,
                    ]
                );

                $subblockId = $scrollData->addSubBlock($group->getCode());
                $scrollData->addSubBlockData($group->getCode(), $subblockId, $html, $fieldName);
            }
        }

        $this->removeEmptyGroupBlocks($scrollData);

        $this->eventType = null;
    }

    /**
     * @param BeforeListRenderEvent $event
     */
    public function onEdit(BeforeListRenderEvent $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof AttributeFamilyAwareInterface) {
            return;
        }

        $scrollData = $event->getScrollData();
        $formView = $event->getFormView();
        $groupsData = $this->attributeManager->getGroupsWithAttributes($entity->getAttributeFamily());
        $this->filterGroupAttributes($groupsData, 'form', 'is_enabled');
        $this->addNotEmptyGroupBlocks($scrollData, $groupsData);

        foreach ($groupsData as $groupsDatum) {
            /** @var AttributeGroup $group */
            $group = $groupsDatum['group'];
            /** @var FieldConfigModel $attribute */
            foreach ($groupsDatum['attributes'] as $attribute) {
                $fieldId = $attribute->getFieldName();
                if (in_array($fieldId, $this->getRestrictedToMoveFields(), true)) {
                    continue;
                }
                $attributeView = $formView->offsetGet($fieldId);

                if (!$attributeView->isRendered()) {
                    $html = $event->getEnvironment()->render('@OroEntityConfig/Attribute/row.html.twig', [
                        'child' => $attributeView,
                    ]);

                    $subblockId = $scrollData->addSubBlock($group->getCode());
                    $scrollData->addSubBlockData($group->getCode(), $subblockId, $html, $fieldId);
                } else {
                    $this->moveFieldToBlock($scrollData, $attribute->getFieldName(), $group->getCode());
                }
            }
        }

        $this->removeEmptyGroupBlocks($scrollData);
    }

    /**
     * @param ScrollData $scrollData
     */
    private function removeEmptyGroupBlocks(ScrollData $scrollData)
    {
        $data = $scrollData->getData();
        if (empty($data[ScrollData::DATA_BLOCKS])) {
            return;
        }

        foreach ($data[ScrollData::DATA_BLOCKS] as $blockId => $data) {
            if (!is_string($blockId)) {
                continue;
            }
            $isEmpty = true;
            if (!empty($data[ScrollData::SUB_BLOCKS])) {
                foreach ($data[ScrollData::SUB_BLOCKS] as $subblockId => $subblockData) {
                    if (!empty($subblockData[ScrollData::DATA])) {
                        $isEmpty = false;
                    }
                }
            }

            if ($isEmpty) {
                $scrollData->removeNamedBlock($blockId);
            }
        }
    }

    /**
     * @param array $groups
     * @param string $scope
     * @param string $option
     */
    private function filterGroupAttributes(array &$groups, $scope, $option)
    {
        foreach ($groups as &$group) {
            $group['attributes'] = array_filter(
                $group['attributes'],
                function (FieldConfigModel $attribute = null) use ($scope, $option) {
                    if ($attribute) {
                        $attributeScopedConfig = $attribute->toArray($scope);
                        return !empty($attributeScopedConfig[$option]);
                    }

                    return false;
                }
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function moveFieldToBlock(ScrollData $scrollData, $fieldName, $blockId)
    {
        if ($this->eventType === self::EVENT_TYPE_VIEW) {
            if (in_array($fieldName, $this->getRestrictedToMoveFields(), true)) {
                return;
            }
        }

        parent::moveFieldToBlock($scrollData, $fieldName, $blockId);
    }

    /**
     * @return array
     */
    protected function getRestrictedToMoveFields()
    {
        return $this->fieldsRestrictedToMove;
    }

    protected function addNotEmptyGroupBlocks(ScrollData $scrollData, array $groups)
    {
        parent::addNotEmptyGroupBlocks($scrollData, $groups);

        foreach ($groups as $group) {
            if (empty($group['attributes'])) {
                continue;
            }

            /** @var AttributeGroup $currentGroup */
            $currentGroup = $group['group'];

            $block = $scrollData->getBlock($currentGroup->getCode());

            $priority = $block[ScrollData::PRIORITY] ?? self::DEFAULT_PRIORITY;

            /** @var FieldConfigModel $attribute */
            foreach ($group['attributes'] as $attribute) {
                $config = $this->entityConfigProvider->getConfig(Product::class, $attribute->getFieldName());

                $scrollData->addNamedBlock(
                    $attribute->getFieldName(),
                    $this->translator->trans((string) $config->get('label')),
                    ++$priority
                );
            }
        }
    }
}
