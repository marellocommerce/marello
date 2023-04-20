<?php

namespace Marello\Bundle\WebhookBundle\Integration\Connector;

use Marello\Bundle\WebhookBundle\Entity\Webhook;
use Marello\Bundle\WebhookBundle\Model\WebhookProvider;

use Oro\Bundle\BatchBundle\Item\ExecutionContext;
use Oro\Bundle\ImportExportBundle\Context\ContextInterface;
use Oro\Bundle\ImportExportBundle\Context\ContextRegistry;
use Oro\Bundle\IntegrationBundle\Logger\LoggerStrategy;
use Oro\Bundle\IntegrationBundle\Provider\AbstractConnector;
use Oro\Bundle\IntegrationBundle\Provider\ConnectorContextMediator;
use Oro\Bundle\IntegrationBundle\Provider\TwoWaySyncConnectorInterface;
use Psr\Log\LoggerAwareInterface;

class WebhookNotificationConnector extends AbstractConnector implements TwoWaySyncConnectorInterface
{
    public const EXPORT_JOB_NAME = 'marello_webhook_notification';
    public const TYPE = 'marello_webhook';

    protected WebhookProvider $webhookProvider;

    /**
     * @param ContextRegistry $contextRegistry
     * @param LoggerStrategy $logger
     * @param ConnectorContextMediator $contextMediator
     * @param WebhookProvider $webhookProvider
     */
    public function __construct(
        ContextRegistry $contextRegistry,
        LoggerStrategy $logger,
        ConnectorContextMediator $contextMediator,
        WebhookProvider $webhookProvider
    ) {
        parent::__construct($contextRegistry, $logger, $contextMediator);
        $this->logger          = $logger;
        $this->contextMediator = $contextMediator;
        $this->webhookProvider = $webhookProvider;
    }

    /**
     * @inheritDoc
     */
    protected function getConnectorSource()
    {
        $executionContext = $this->getJobExecutionContext();
        $items = $executionContext->get('items');
        $iterator = new \AppendIterator;
        $iterator->append(new \ArrayIterator($items));
        return $iterator;
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeFromContext(ContextInterface $context)
    {
        $this->transport = $this->contextMediator->getTransport($context, true);
        $this->channel   = $this->contextMediator->getChannel($context);

        $this->validateConfiguration();
        $executionContext = $this->getJobExecutionContext();
        $webhookEntity = $this->webhookProvider->getWebhookById($executionContext->get('webhook_id'));
        $this->transport->setWebhook($webhookEntity);
        $this->transport->init($this->channel->getTransport());
        $this->setSourceIterator($this->getConnectorSource());

        if ($this->getSourceIterator() instanceof LoggerAwareInterface) {
            $this->getSourceIterator()->setLogger($this->logger);
        }
    }

    /**
     * @return ExecutionContext
     */
    protected function getJobExecutionContext(): ExecutionContext
    {
        return $this->stepExecution
            ->getJobExecution()
            ->getExecutionContext();
    }


    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return 'marello.webhook.connector.notification.label';
    }

    /**
     * {@inheritdoc}
     */
    public function getImportEntityFQCN(): string
    {
        return Webhook::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getImportJobName(): string
    {
        return self::EXPORT_JOB_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return self::TYPE;
    }

    public function getExportJobName()
    {
        return self::EXPORT_JOB_NAME;
    }
}
