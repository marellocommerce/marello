<?php

namespace Marello\Bundle\SalesBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Oro\Bundle\LocaleBundle\Entity\Localization;

use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\SalesBundle\Entity\SalesChannelType;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

class LoadSalesChannelData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var array
     */
    protected $data = [
        ['name' => 'Main','code' => 'main','type' => LoadSalesChannelTypesData::MARELLO, 'currency' => 'EUR'],
    ];

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            LoadSalesChannelTypesData::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->loadSalesChannels();
    }

    /**
     * load and create SalesChannels
     */
    protected function loadSalesChannels()
    {
        $organization = $this->manager->getRepository(Organization::class)->getFirst();
        $localization = $this->manager->getRepository(Localization::class)->find(1);
        $i            = 1;

        foreach ($this->data as $values) {
            $channel = new SalesChannel($values['name']);
            $channel->setChannelType($this->findTypeByName($values['type']));
            $channel->setCode($values['code']);
            $channel->setCurrency($values['currency']);
            $channel->setOrganization($organization);
            $channel->setLocalization($localization);

            $this->manager->persist($channel);
            $i++;
        }

        $this->manager->flush();
    }

    /**
     * @param string $name
     * @return null|SalesChannelType
     */
    private function findTypeByName($name)
    {
        $type = $this->manager
            ->getRepository(SalesChannelType::class)
            ->find($name);

        if ($type) {
            return $type;
        }

        return null;
    }
}
