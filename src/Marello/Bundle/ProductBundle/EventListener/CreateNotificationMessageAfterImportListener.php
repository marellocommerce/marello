<?php

namespace Marello\Bundle\ProductBundle\EventListener;

use Doctrine\Persistence\ManagerRegistry;
use Marello\Bundle\NotificationMessageBundle\Event\CreateNotificationMessageEvent;
use Marello\Bundle\NotificationMessageBundle\Factory\NotificationMessageContextFactory;
use Marello\Bundle\NotificationMessageBundle\Provider\NotificationMessageSourceInterface;
use Marello\Bundle\PricingBundle\Entity\AssembledChannelPriceList;
use Marello\Bundle\PricingBundle\Entity\AssembledPriceList;
use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\EmailBundle\Model\EmailTemplateCriteria;
use Oro\Bundle\EmailBundle\Provider\LocalizedTemplateProvider;
use Oro\Bundle\ImportExportBundle\Async\ImportExportResultSummarizer;
use Oro\Bundle\ImportExportBundle\Async\Topic\SendImportNotificationTopic;
use Oro\Bundle\ImportExportBundle\Entity\ImportExportResult;
use Oro\Bundle\ImportExportBundle\Processor\ProcessorRegistry;
use Oro\Bundle\MessageQueueBundle\Entity\Job;
use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        private LocalizedTemplateProvider $localizedTemplateProvider,
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
        $user = $this->managerRegistry->getManagerForClass(User::class)
            ->getRepository(User::class)
            ->find($importExportResult->getOwner());
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
                break;
            case AssembledChannelPriceList::class:
                $message = $title.'.channel_prices';
                break;
            default:
                $message = $title.'.products';
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
        $templateCollection = $this->localizedTemplateProvider->getAggregated(
            [$user],
            new EmailTemplateCriteria($template),
            $summary
        );

        foreach ($templateCollection as $template) {
            $context = NotificationMessageContextFactory::createInfo(
                NotificationMessageSourceInterface::NOTIFICATION_MESSAGE_SOURCE_SYSTEM,
                $this->translator->trans($title.'.title'),
                $this->translator->trans($message),
                $template->getEmailTemplate()->getContent(),
            );
            $this->eventDispatcher->dispatch(
                new CreateNotificationMessageEvent($context),
                CreateNotificationMessageEvent::NAME
            );
        }
    }
}
