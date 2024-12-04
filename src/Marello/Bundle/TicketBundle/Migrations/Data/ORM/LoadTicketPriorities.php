<?php

namespace Marello\Bundle\TicketBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Marello\Bundle\TicketBundle\Provider\TicketPriorityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\Repository\EnumValueRepository;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;

class LoadTicketPriorities extends AbstractFixture
{
    /** @var array */
    protected $data = [
        [
            'id' => TicketPriorityInterface::TICKET_PRIORITY_NORMAL,
            'name' => 'Normal',
            'isDefault' => true
        ],
        [
            'id' => TicketPriorityInterface::TICKET_PRIORITY_LOW,
            'name' => 'Low',
            'isDefault' => false
        ],
        [
            'id' => TicketPriorityInterface::TICKET_PRIORITY_HIGH,
            'name' => 'High',
            'isDefault' => false
        ]
    ];

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadPriorities($manager);
        $manager->flush();
    }

    protected function loadPriorities(ObjectManager $manager): void
    {
        $className = ExtendHelper::buildEnumValueClassName(
            TicketPriorityInterface::TICKET_PRIORITY_ENUM_CODE
        );

        /** @var EnumValueRepository $enumRepo */
        $enumRepo = $manager->getRepository($className);
        $priority = 1;
        foreach ($this->data as $item) {
            $existingEnum = $enumRepo->find($item['id']);
            if (!$existingEnum) {
                $enumOption = $enumRepo->createEnumValue($item['name'], $priority++, $item['isDefault'], $item['id']);
                $manager->persist($enumOption);
            }
        }
    }
}
