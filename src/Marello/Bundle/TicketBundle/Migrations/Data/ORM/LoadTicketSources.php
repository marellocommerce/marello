<?php

namespace Marello\Bundle\TicketBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Marello\Bundle\TicketBundle\Provider\TicketSourceInterface;
use Oro\Bundle\EntityExtendBundle\Entity\Repository\EnumValueRepository;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;

class LoadTicketSources extends AbstractFixture
{
    /** @var array */
    protected $data = [
        [
            'id' => TicketSourceInterface::TICKET_SOURCE_WEB,
            'name' => 'Web',
            'isDefault' => false
        ],
        [
            'id' => TicketSourceInterface::TICKET_SOURCE_EMAIL,
            'name' => 'E-mail',
            'isDefault' => false
        ],
        [
            'id' => TicketSourceInterface::TICKET_SOURCE_PHONE,
            'name' => 'Phone',
            'isDefault' => false
        ],
        [
            'id' => TicketSourceInterface::TICKET_SOURCE_OTHER,
            'name' => 'Other',
            'isDefault' => false
        ]
    ];

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadSources($manager);
        $manager->flush();
    }

    protected function loadSources(ObjectManager $manager): void
    {
        $className = ExtendHelper::buildEnumValueClassName(
            TicketSourceInterface::TICKET_SOURCE_ENUM_CODE
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
