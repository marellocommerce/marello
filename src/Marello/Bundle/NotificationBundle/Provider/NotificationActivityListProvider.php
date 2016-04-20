<?php

namespace Marello\Bundle\NotificationBundle\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Marello\Bundle\NotificationBundle\Entity\Notification;
use Oro\Bundle\ActivityListBundle\Entity\ActivityList;
use Oro\Bundle\ActivityListBundle\Entity\ActivityOwner;
use Oro\Bundle\ActivityListBundle\Model\ActivityListProviderInterface;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityConfigBundle\Config\Id\ConfigIdInterface;
use Oro\Bundle\EntityConfigBundle\DependencyInjection\Utils\ServiceLink;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Symfony\Component\Translation\TranslatorInterface;

class NotificationActivityListProvider implements ActivityListProviderInterface
{
    /** @var DoctrineHelper */
    protected $doctrineHelper;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var ServiceLink */
    protected $entityManagerLink;

    /**
     * NotificationActivityListProvider constructor.
     *
     * @param DoctrineHelper      $doctrineHelper
     * @param TranslatorInterface $translator
     * @param ServiceLink         $entityManagerLink
     */
    public function __construct(
        DoctrineHelper $doctrineHelper,
        TranslatorInterface $translator,
        ServiceLink $entityManagerLink
    ) {
        $this->doctrineHelper    = $doctrineHelper;
        $this->translator        = $translator;
        $this->entityManagerLink = $entityManagerLink;
    }

    /**
     * Returns true if given target $configId is supported by activity
     *
     * @param ConfigIdInterface $configId
     * @param ConfigManager     $configManager
     *
     * @return bool
     */
    public function isApplicableTarget(ConfigIdInterface $configId, ConfigManager $configManager)
    {
        $provider = $configManager->getProvider('activity');

        return $provider->hasConfigById($configId)
        && $provider->getConfigById($configId)->has('activities')
        && in_array(Notification::class, $provider->getConfigById($configId)->get('activities'));
    }

    /**
     * @param object|Notification $entity
     *
     * @return string
     */
    public function getSubject($entity)
    {
        return $this->translator->trans($entity->getTemplate()->getName(), [], 'MarelloNotification');
    }

    /**
     * @param object|Notification $entity
     *
     * @return string|null
     */
    public function getDescription($entity)
    {
        return null;
    }

    /**
     * Get array of ActivityOwners for list entity
     *
     * @param object       $entity
     * @param ActivityList $activityList
     *
     * @return ActivityOwner[]
     */
    public function getActivityOwners($entity, ActivityList $activityList)
    {
        return [];
    }

    /**
     * @param ActivityList $activityListEntity
     *
     * @return array
     */
    public function getData(ActivityList $activityListEntity)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->entityManagerLink->getService();

        /** @var Notification $entity */
        $entity = $em
            ->getRepository($activityListEntity->getRelatedActivityClass())
            ->find($activityListEntity->getRelatedActivityId());

        return [
            'body' => $entity->getBody(),
        ];
    }

    /**
     * @param object $activityEntity
     *
     * @return Organization|null
     */
    public function getOrganization($activityEntity)
    {
        return $activityEntity->getOrganization();
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'MarelloNotificationBundle:Notification/js:activityItemTemplate.js.twig';
    }

    /**
     * Should return array of route names as key => value
     * e.g. [
     *      'itemView'  => 'item_view_route',
     *      'itemEdit'  => 'item_edit_route',
     *      'itemDelete => 'item_delete_route'
     * ]
     *
     * @return array
     */
    public function getRoutes()
    {
        return [];
    }

    /**
     * returns a class name of entity for which we monitor changes
     *
     * @return string
     */
    public function getActivityClass()
    {
        return Notification::class;
    }

    /**
     * returns a class name of entity for which we verify ACL
     *
     * @return string
     */
    public function getAclClass()
    {
        return Notification::class;
    }

    /**
     * @param object $entity
     *
     * @return integer
     */
    public function getActivityId($entity)
    {
        return $this->doctrineHelper->getSingleEntityIdentifier($entity);
    }

    /**
     * Check if provider supports given activity
     *
     * @param  object $entity
     *
     * @return bool
     */
    public function isApplicable($entity)
    {
        return $this->doctrineHelper->getEntityClass($entity) === Notification::class;
    }

    /**
     * Returns array of assigned entities for activity
     *
     * @param object $entity
     *
     * @return array
     */
    public function getTargetEntities($entity)
    {
        return $entity->getActivityTargetEntities();
    }
}
