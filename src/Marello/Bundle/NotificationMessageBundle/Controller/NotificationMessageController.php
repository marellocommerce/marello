<?php

namespace Marello\Bundle\NotificationMessageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;

use Marello\Bundle\NotificationMessageBundle\Entity\NotificationMessage;
use Marello\Bundle\NotificationMessageBundle\Provider\NotificationMessageTypeInterface;
use Marello\Bundle\NotificationMessageBundle\Entity\Repository\NotificationMessageRepository;

class NotificationMessageController extends AbstractController
{
    /**
     * @return array
     */
    #[Route(path: '/', name: 'marello_notificationmessage_index')]
    #[Template('@MarelloNotificationMessage/NotificationMessage/index.html.twig')]
    #[AclAncestor('marello_notificationmessage_view')]
    public function indexAction(): array
    {
        return [
            'entity_class' => NotificationMessage::class,
        ];
    }

    /**
     * @param NotificationMessage $entity
     * @return array
     */
    #[Route(path: '/view/{id}', name: 'marello_notificationmessage_view', requirements: ['id' => '\d+'])]
    #[Template('@MarelloNotificationMessage/NotificationMessage/view.html.twig')]
    #[AclAncestor('marello_notificationmessage_view')]
    public function viewAction(NotificationMessage $entity): array
    {
        return ['entity' => $entity];
    }

    #[Route(
        path: '/widget/sidebar-notification-messages/{perPage}',
        name: 'marello_notificationmessage_widget_sidebar_notification_messages',
        requirements: ['perPage' => '\d+'],
        defaults: ['perPage' => 10]
    )]
    #[AclAncestor('marello_notificationmessage_view')]
    public function notificationMessagesWidgetAction(Request $request, int $perPage): Response
    {
        /** @var NotificationMessageRepository $repository */
        $repository = $this->container->get('doctrine')->getRepository(NotificationMessage::class);
        /** @var User $user */
        $user = $this->getUser();
        $types = $this->extractTypes($request);
        $notificationMessages = $repository->getNotificationMessagesAssignedTo($user, $perPage, $types);

        return $this->render(
            '@MarelloNotificationMessage/NotificationMessage/widget/notificationMessagesWidget.html.twig',
            ['notificationMessages' => $notificationMessages]
        );
    }

    protected function extractTypes(Request $request): array
    {
        $possibleTypes = [
            NotificationMessageTypeInterface::NOTIFICATION_MESSAGE_TYPE_ERROR,
            NotificationMessageTypeInterface::NOTIFICATION_MESSAGE_TYPE_WARNING,
            NotificationMessageTypeInterface::NOTIFICATION_MESSAGE_TYPE_SUCCESS,
            NotificationMessageTypeInterface::NOTIFICATION_MESSAGE_TYPE_INFO,
        ];
        $types = $request->get('types', []);
        if ($types) {
            $types = array_keys($types);
            foreach ($types as $key => $typeName) {
                if (!\in_array($typeName, $possibleTypes)) {
                    unset($types[$key]);
                }
            }
        }

        return $types;
    }

    protected function getEventDispatcher()
    {
        return $this->container->get(EventDispatcherInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                EventDispatcherInterface::class,
                EntityRoutingHelper::class,
            ]
        );
    }
}
