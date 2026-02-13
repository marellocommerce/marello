<?php

namespace Marello\Bundle\CoreBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

use Oro\Component\MessageQueue\Client\MessageProducer;

use Marello\Bundle\CoreBundle\Async\Topic\SequenceNumberEntityCreationTopic;

class AddSequenceCreationForOrganizations extends AbstractFixture implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $organizations = $this->manager->getRepository(Organization::class)->findAll();
        foreach ($organizations as $organization) {
            $this->createSquencesForOrganizations($organization);
        }
    }

    /**
     * @return MessageProducer
     */
    protected function getMessageProducer()
    {
        return $this->container->get('oro_message_queue.client.message_producer');
    }

    protected function createSquencesForOrganizations(Organization $organization)
    {
        $this->getMessageProducer()->send(
            SequenceNumberEntityCreationTopic::getName(),
            [
                'organizationId' => $organization->getId()
            ]
        );
    }
}
