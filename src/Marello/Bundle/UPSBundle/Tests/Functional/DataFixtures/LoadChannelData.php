<?php

namespace Marello\Bundle\UPSBundle\Tests\Functional\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Migrations\Data\ORM\LoadAdminUserData;

use Marello\Bundle\UPSBundle\Method\UPSShippingMethod;

class LoadChannelData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
{
    /**
     * @var array Channels configuration
     */
    protected $channelData = [
        [
            'name' => 'UPS1',
            'type' => UPSShippingMethod::IDENTIFIER,
            'transport' => 'ups:transport_1',
            'enabled' => true,
            'reference' => 'ups:channel_1',
        ],
        [
            'name' => 'UPS2',
            'type' => UPSShippingMethod::IDENTIFIER,
            'transport' => 'ups:transport_2',
            'enabled' => true,
            'reference' => 'ups:channel_2',
        ]
    ];

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('oro_user.manager');
        $admin = $userManager->findUserByEmail(LoadAdminUserData::DEFAULT_ADMIN_EMAIL);
        $organization = $manager->getRepository(Organization::class)->getFirst();

        foreach ($this->channelData as $data) {
            $entity = new Channel();
            /** @var Transport $transport */
            $transport = $this->getReference($data['transport']);
            $entity->setName($data['name']);
            $entity->setType($data['type']);
            $entity->setDefaultUserOwner($admin);
            $entity->setOrganization($organization);
            $entity->setTransport($transport);
            $this->setReference($data['reference'], $entity);

            $manager->persist($entity);
        }
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            __NAMESPACE__ . '\LoadTransportData'
        ];
    }
}
