<?php

namespace Marello\Bundle\ShippingBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Marello\Bundle\ShippingBundle\Method\Event\MethodRenamingEventDispatcherInterface;
use Oro\Bundle\ConfigBundle\Entity\ConfigValue;
use Oro\Bundle\ConfigBundle\Entity\Repository\ConfigValueRepository;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractMoveConfigValuesToSettings extends AbstractFixture implements ContainerAwareInterface
{
    const SECTION_NAME = '';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var bool
     */
    protected $installed;

    /**
     * @var MethodRenamingEventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->doctrine = $container->get('doctrine');
        $this->installed = $container->hasParameter('installed') && $container->getParameter('installed');
        $this->dispatcher = $container->get('marello_shipping.method.event.dispatcher.method_renaming');
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        if ($this->installed) {
            $this->moveConfigFromSystemConfigToIntegration($manager, $this->getOrganization());
            if ('' !== static::SECTION_NAME) {
                $this->getConfigValueRepository()->removeBySection(static::SECTION_NAME);
            }
        }
    }

    /**
     * @param ObjectManager         $manager
     * @param OrganizationInterface $organization
     */
    abstract protected function moveConfigFromSystemConfigToIntegration(
        ObjectManager $manager,
        OrganizationInterface $organization
    );

    /**
     * @return ConfigValueRepository
     */
    protected function getConfigValueRepository()
    {
        return $this->doctrine->getManagerForClass(ConfigValue::class)->getRepository(ConfigValue::class);
    }

    /**
     * @return Organization
     */
    protected function getOrganization()
    {
        return $this->doctrine->getRepository(Organization::class)->getFirst();
    }
}
