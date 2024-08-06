<?php

namespace Marello\Bundle\NotificationMessageBundle\Tests\Unit\EventListener;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;

use PHPUnit\Framework\TestCase;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\EntityExtendBundle\Tests\Unit\Fixtures\TestEnumValue;
use Oro\Bundle\EntityExtendBundle\Entity\Repository\EnumValueRepository;

use Marello\Bundle\NotificationMessageBundle\Entity\NotificationMessage;
use Marello\Bundle\NotificationMessageBundle\Datagrid\ActionPermissionProvider;
use Marello\Bundle\NotificationMessageBundle\Factory\NotificationMessageFactory;
use Marello\Bundle\NotificationMessageBundle\Event\CreateNotificationMessageEvent;
use Marello\Bundle\NotificationMessageBundle\Factory\NotificationMessageContextFactory;
use Marello\Bundle\NotificationMessageBundle\Provider\NotificationMessageTypeInterface;
use Marello\Bundle\NotificationMessageBundle\Provider\NotificationMessageSourceInterface;
use Marello\Bundle\NotificationMessageBundle\Provider\NotificationMessageResolvedInterface;
use Marello\Bundle\NotificationMessageBundle\Entity\Repository\NotificationMessageRepository;
use Marello\Bundle\NotificationMessageBundle\EventListener\NotificationMessageEventListener;

class NotificationMessageEventListenerTest extends TestCase
{
    /** @var DoctrineHelper|\PHPUnit\Framework\MockObject\MockObject */
    private $doctrineHelper;

    /** @var TranslatorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $translator;

    /** @var MessageProducerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $messageProducer;

    /** @var NotificationMessageFactory|\PHPUnit\Framework\MockObject\MockObject */
    private $notificationFactory;

    /** @var ActionPermissionProvider */
    private $listener;

    protected function setUp(): void
    {
        $this->doctrineHelper = $this->createMock(DoctrineHelper::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->translator->expects($this->any())
            ->method('trans')
            ->willReturnCallback(function ($message, $arguments, $domain) {
                return $message . $domain;
            });

        $this->messageProducer = $this->createMock(MessageProducerInterface::class);
        $this->notificationFactory = $this->createMock(NotificationMessageFactory::class);
        $this->listener = new NotificationMessageEventListener(
            $this->doctrineHelper,
            $this->translator,
            $this->messageProducer,
            $this->notificationFactory
        );
    }

    public function testOnCreateNew(): void
    {
        $entity = new \stdClass();
        $context = NotificationMessageContextFactory::createError(
            NotificationMessageSourceInterface::NOTIFICATION_MESSAGE_SOURCE_ALLOCATION,
            'test_title',
            'test_message',
            'test_solution',
            $entity
        );
        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->expects($this->once())
            ->method('getIdentifierValues')
            ->with($entity)
            ->willReturn(['id' => 1]);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())
            ->method('getClassMetadata')
            ->with(get_class($entity))
            ->willReturn($classMetadata);
        $this->doctrineHelper->expects($this->once())
            ->method('getEntityManagerForClass')
            ->with(NotificationMessage::class)
            ->willReturn($em);
        $notMessageRepo = $this->createMock(NotificationMessageRepository::class);
        $enumRepo = $this->createMock(EnumValueRepository::class);
        $em->expects($this->once())
            ->method('getRepository')
            ->withConsecutive(
                [NotificationMessage::class]
            )
            ->willReturnOnConsecutiveCalls($notMessageRepo);
        $notMessageRepo->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);
        $em->expects($this->once())->method('persist');
        $em->expects($this->once())->method('flush');

        $event = new CreateNotificationMessageEvent($context);
        $this->listener->onCreate($event);
    }

    public function testOnCreateOld(): void
    {
        $entity = new \stdClass();
        $context = NotificationMessageContextFactory::createError(
            NotificationMessageSourceInterface::NOTIFICATION_MESSAGE_SOURCE_ALLOCATION,
            'test_title',
            'test_message',
            'test_solution',
            $entity
        );
        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->expects($this->once())
            ->method('getIdentifierValues')
            ->with($entity)
            ->willReturn(['id' => 1]);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())
            ->method('getClassMetadata')
            ->with(get_class($entity))
            ->willReturn($classMetadata);
        $this->doctrineHelper->expects($this->once())
            ->method('getEntityManagerForClass')
            ->with(NotificationMessage::class)
            ->willReturn($em);
        $notMessageRepo = $this->createMock(NotificationMessageRepository::class);
        $em->expects($this->once())
            ->method('getRepository')
            ->with(NotificationMessage::class)
            ->willReturn($notMessageRepo);
        $notificationMessage = new NotificationMessage();
        $notMessageRepo->expects($this->once())
            ->method('findOneBy')
            ->willReturn($notificationMessage);
        $em->expects($this->never())->method('persist');
        $em->expects($this->once())->method('flush');

        $event = new CreateNotificationMessageEvent($context);
        $this->listener->onCreate($event);
        $this->assertEquals(2, $notificationMessage->getCount());
    }
}