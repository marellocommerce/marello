<?php

namespace Marello\Bundle\TicketBundle\Migrations\Data\ORM;

use Marello\Bundle\TicketBundle\Provider\TicketStatusInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\EntityExtendBundle\Entity\Repository\EnumValueRepository;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;

class LoadTicketStatuses extends AbstractFixture
{
    /** @var array */
    protected $data = [
        [
            'id' => TicketStatusInterface::TICKET_STATUS_OPEN,
            'name' => 'Open',
            'isDefault' => true
        ],
        [
            'id' => TicketStatusInterface::TICKET_STATUS_IN_PROGRESS,
            'name' => 'In Progress',
            'isDefault' => false
        ],
        [
            'id' => TicketStatusInterface::TICKET_STATUS_RESOLVED,
            'name' => 'Resolved',
            'isDefault' => false
        ],
        [
            'id' => TicketStatusInterface::TICKET_STATUS_CLOSED,
            'name' => 'Closed',
            'isDefault' => false
        ]
    ];

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadStatuses($manager);
        $manager->flush();
    }

    protected function loadStatuses(ObjectManager $manager): void
    {
        $className = ExtendHelper::buildEnumValueClassName(
            TicketStatusInterface::TICKET_STATUS_ENUM_CODE
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