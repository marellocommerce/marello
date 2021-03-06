<?php

namespace Marello\Bundle\NotificationBundle\Tests\Functional\Email;

use Oro\Bundle\NotificationBundle\Async\Topics;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\MessageQueueBundle\Test\Functional\MessageQueueExtension;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\NotificationBundle\Email\SendProcessor;
use Marello\Bundle\NotificationBundle\Entity\Notification;
use Marello\Bundle\OrderBundle\Tests\Functional\DataFixtures\LoadOrderData;
use Marello\Bundle\NotificationBundle\Exception\MarelloNotificationException;

class SendProcessorTest extends WebTestCase
{
    use MessageQueueExtension;

    /** @var SendProcessor */
    protected $sendProcessor;

    public function setUp()
    {
        $this->initClient($this->generateBasicAuthHeader());

        $this->loadFixtures(
            [
                LoadOrderData::class,
            ]
        );

        $this->sendProcessor = $this->getContainer()->get('marello_notification.email.send_processor');
    }

    /**
     * @covers SendProcessor::sendNotification
     */
    public function testSendsNotification()
    {
        /** @var Order $order */
        $order = $this->getReference('marello_order_0');
        $notificationsBefore = count(
            $this->getContainer()
                ->get('doctrine')
                ->getRepository(Notification::class)
                ->findAll()
        );

        $this->sendProcessor->sendNotification(
            'marello_order_accepted_confirmation',
            [$order->getCustomer()],
            $order
        );

        $notificationsAfter = count(
            $this->getContainer()
                ->get('doctrine')
                ->getRepository(Notification::class)
                ->findAll()
        );

        $this->assertEquals(1, $notificationsAfter - $notificationsBefore);
    }

    /**
     * @throws MarelloNotificationException
     * @throws \Oro\Bundle\NotificationBundle\Exception\NotificationSendException
     * @throws \Twig_Error
     */
    public function testExceptionisThrownWhenTemplateIsNotFoundForEntity()
    {
        /** @var Order $order */
        $order = $this->getReference('marello_order_0');

        $this->expectExceptionMessageRegExp(
            '/Email template with name .* for entity .* was not found. Check if such template exists./'
        );
        $this->expectException(MarelloNotificationException::class);


        $this->sendProcessor->sendNotification(
            'no_valid_template',
            [$order->getCustomer()],
            $order
        );
    }

    /**
     * @throws MarelloNotificationException
     * @throws \Oro\Bundle\NotificationBundle\Exception\NotificationSendException
     * @throws \Twig_Error
     */
    public function testMessageSendIsRenderedTemplateAndSubject()
    {
        /** @var Order $order */
        $order = $this->getReference('marello_order_0');
        $this->sendProcessor->sendNotification(
            'marello_order_accepted_confirmation',
            [$order->getCustomer()],
            $order
        );

        self::assertMessageSent(Topics::SEND_NOTIFICATION_EMAIL);
        $message = self::getSentMessage(Topics::SEND_NOTIFICATION_EMAIL);

        self::assertNotContains('{{ entity', $message['subject']);
        self::assertNotContains('{{ entity', $message['body']);
        self::assertEquals('text/html', $message['contentType']);
        self::assertContains($order->getOrderNumber(), $message['subject']);
        self::assertContains($order->getOrderNumber(), $message['body']);
    }
}
