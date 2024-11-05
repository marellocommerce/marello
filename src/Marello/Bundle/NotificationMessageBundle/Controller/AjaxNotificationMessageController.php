<?php

namespace Marello\Bundle\NotificationMessageBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\SecurityBundle\Attribute\CsrfProtection;
use Oro\Bundle\EntityExtendBundle\Entity\Repository\EnumValueRepository;

use Marello\Bundle\NotificationMessageBundle\Entity\NotificationMessage;
use Marello\Bundle\NotificationMessageBundle\Provider\NotificationMessageResolvedInterface;

class AjaxNotificationMessageController extends AbstractController
{
    /**
     *
     * @param NotificationMessage $entity
     * @return JsonResponse
     */
    #[Route(path: '/resolve/{id}', methods: ['POST'], name: 'marello_notificationmessage_resolve', requirements: ['id' => '\d+'])]
    #[AclAncestor('marello_notificationmessage_update')]
    #[CsrfProtection]
    public function resolveAction(NotificationMessage $entity)
    {
        try {
            $em = $this->container->get(ManagerRegistry::class)->getManagerForClass(NotificationMessage::class);
            $className = ExtendHelper::buildEnumValueClassName(
                NotificationMessageResolvedInterface::NOTIFICATION_MESSAGE_RESOLVED_ENUM_CODE
            );
            /** @var EnumValueRepository $enumRepo */
            $enumRepo = $em->getRepository($className);
            $resolvedYes = $enumRepo->find(NotificationMessageResolvedInterface::NOTIFICATION_MESSAGE_RESOLVED_YES);
            $entity->setResolved($resolvedYes);

            $em->flush();
        } catch (\Throwable $exception) {
            return new JsonResponse(
                [
                    'successfull' => false,
                    'message' => $exception->getMessage(),
                ]
            );
        }

        return new JsonResponse(['successful' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                ManagerRegistry::class
            ]
        );
    }
}
