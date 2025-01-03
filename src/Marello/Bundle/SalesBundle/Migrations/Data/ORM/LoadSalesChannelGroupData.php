<?php

namespace Marello\Bundle\SalesBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\MigrationBundle\Fixture\VersionedFixtureInterface;

use Marello\Bundle\SalesBundle\Entity\SalesChannelGroup;

class LoadSalesChannelGroupData extends AbstractFixture implements VersionedFixtureInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var array
     */
    protected $data = [
        [
            'name' => 'N/A',
            'description' => 'System Sales Channel Group',
            'system' => true
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->loadSalesChannelGroups();
    }

    /**
     * load and create SalesChannels
     */
    protected function loadSalesChannelGroups()
    {
        $organization = $this->manager->getRepository(Organization::class)->getFirst();
        $existingGroup = $this
            ->manager
            ->getRepository(SalesChannelGroup::class)
            ->findOneBy([
                'system' => true,
                'organization'=> $organization
            ]);
        foreach ($this->data as $values) {
            $group = ($existingGroup) ?: new SalesChannelGroup();
            $group
                ->setName($values['name'])
                ->setDescription(sprintf('%s for %s organization', $values['description'], $organization->getName()))
                ->setSystem($values['system'])
                ->setOrganization($organization);

            $this->manager->persist($group);
        }

        $this->manager->flush();
    }


    /**
     * {@inheritDoc}
     * @return string
     */
    public function getVersion()
    {
        return '1.0';
    }
}
