<?php

namespace Marello\Bundle\ProductBundle\EventListener;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use Oro\Bundle\MessageQueueBundle\Entity\Job;
use Oro\Bundle\EmailBundle\Entity\EmailTemplate;
use Oro\Bundle\EmailBundle\Model\EmailTemplateCriteria;
use Oro\Bundle\ImportExportBundle\Entity\ImportExportResult;
use Oro\Bundle\ImportExportBundle\Processor\ProcessorRegistry;
use Oro\Bundle\EmailBundle\Provider\RenderedEmailTemplateProvider;
use Oro\Bundle\ImportExportBundle\Async\ImportExportResultSummarizer;
use Oro\Bundle\ImportExportBundle\Async\Topic\SendImportNotificationTopic;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\PricingBundle\Entity\AssembledPriceList;
use Marello\Bundle\PricingBundle\Entity\AssembledChannelPriceList;
use Marello\Bundle\NotificationMessageBundle\Event\CreateNotificationMessageEvent;
use Marello\Bundle\NotificationMessageBundle\Factory\NotificationMessageContextFactory;
use Marello\Bundle\NotificationMessageBundle\Provider\NotificationMessageSourceInterface;

class CreateNotificationMessageAfterImportListener
{
    private const SUPPORTED_TYPES = [
        ProcessorRegistry::TYPE_IMPORT,
        ProcessorRegistry::TYPE_IMPORT_VALIDATION,
    ];
    private const SUPPORTED_ENTITIES = [
        Product::class,
        AssembledPriceList::class,
        AssembledChannelPriceList::class,
    ];

    public function __construct(
        private ManagerRegistry $managerRegistry,
        private EventDispatcherInterface $eventDispatcher,
        private ImportExportResultSummarizer $importExportResultSummarizer,
        private RenderedEmailTemplateProvider $renderedEmailTemplateProvider,
        private TranslatorInterface $translator
    ) {
    }

    public function postPersist(ImportExportResult $importExportResult): void
    {
        if (!\in_array($importExportResult->getType(),self::SUPPORTED_TYPES)
            || !\in_array($importExportResult->getEntity(), self::SUPPORTED_ENTITIES)
        ) {
            return;
        }

        $job = $this->managerRegistry->getManagerForClass(Job::class)
            ->getRepository(Job::class)
            ->findJobById($importExportResult->getJobId());
        $title = 'marello.product.messages.import.notification_message';
        switch ($importExportResult->getType()) {
            case ProcessorRegistry::TYPE_IMPORT_VALIDATION:
                $template = ImportExportResultSummarizer::TEMPLATE_IMPORT_VALIDATION_RESULT;
                $title .= '.import_validation';
                break;
            default:
                $template = ImportExportResultSummarizer::TEMPLATE_IMPORT_RESULT;
                $title .= '.import';
                break;
        }

        switch ($importExportResult->getEntity()) {
            case AssembledPriceList::class:
                $message = $title.'.prices';
                $type = 'Prices';
                break;
            case AssembledChannelPriceList::class:
                $message = $title.'.channel_prices';
                $type = 'Channel Prices';
                break;
            default:
                $message = $title.'.products';
                $type = 'Products';
                break;
        }

        $originFileName = null;
        $jobData = $job->getData();
        if (!empty($jobData['dependentJobs'])) {
            foreach ($jobData['dependentJobs'] as $dependentJobData) {
                if ($dependentJobData['topic'] === SendImportNotificationTopic::getName()) {
                    $originFileName = $dependentJobData['message']['originFileName'];
                }
            }
        }

        $summary = $this->importExportResultSummarizer->getSummaryResultForNotification($job, $originFileName);
        /** @var EmailTemplate|null $template */
        $template = $this->renderedEmailTemplateProvider
            ->findAndRenderEmailTemplate(
                new EmailTemplateCriteria($template),
                $summary
            );

        if ($template) {
            $context = NotificationMessageContextFactory::createInfo(
                NotificationMessageSourceInterface::NOTIFICATION_MESSAGE_SOURCE_SYSTEM,
                $this->translator->trans($title.'.title', ['type' => $type]),
                $this->translator->trans($message),
                $template->getContent(),
            );
            $this->eventDispatcher->dispatch(
                new CreateNotificationMessageEvent($context),
                CreateNotificationMessageEvent::NAME
            );
        }
    }
}
