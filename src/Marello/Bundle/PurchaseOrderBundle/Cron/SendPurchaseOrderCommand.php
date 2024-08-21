<?php

namespace Marello\Bundle\PurchaseOrderBundle\Cron;

use Doctrine\ORM\QueryBuilder;
use Marello\Bundle\PurchaseOrderBundle\DependencyInjection\Configuration;
use Marello\Bundle\PurchaseOrderBundle\Entity\PurchaseOrder;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Model\WorkflowManager;
use Oro\Bundle\WorkflowBundle\Model\WorkflowStartArguments;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Oro\Bundle\CronBundle\Command\CronCommandScheduleDefinitionInterface;

class SendPurchaseOrderCommand extends Command implements CronCommandScheduleDefinitionInterface
{
    public static $defaultName = 'oro:cron:marello:po-send';

    public function __construct(
        protected ContainerInterface $container,
        protected WorkflowManager $workflowManager
    ) {
        parent::__construct();
    }

    public function getDefaultDefinition()
    {
        return sprintf('* %d * * *', Configuration::DEFAULT_SEND_HOUR);
    }

    public function isActive()
    {
        $featureChecker = $this->container->get('oro_featuretoggle.checker.feature_checker');

        return $featureChecker->isResourceEnabled(self::$defaultName, 'cron_jobs');
    }

    protected function configure()
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Sending Purchase Orders when direct sending is disabled');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->container->get('doctrine');
        /** @var QueryBuilder $qb */
        $qb = $doctrine->getRepository(WorkflowItem::class)->createQueryBuilder('wi');
        $items = $qb->innerJoin('wi.currentStep', 'cs')
            ->andWhere($qb->expr()->eq('wi.entityClass', ':entityClass'))
            ->andWhere($qb->expr()->eq('wi.workflowName', ':workflowName'))
            ->andWhere($qb->expr()->eq('cs.name', ':stepName'))
            ->setParameter('entityClass', PurchaseOrder::class)
            ->setParameter('workflowName', PurchaseOrder::WORKFLOW_NAME)
            ->setParameter('stepName', PurchaseOrder::NOT_SENT_STEP)
            ->getQuery()->getResult();

        $poRepository = $doctrine->getRepository(PurchaseOrder::class);
        $massStartData = [];
        /** @var WorkflowItem $item */
        foreach ($items as $item) {
            $entity = $poRepository->find($item->getEntityId());
            $massStartData[] = new WorkflowStartArguments(
                PurchaseOrder::WORKFLOW_NAME,
                $entity,
                [],
                PurchaseOrder::SEND_TRANSITION
            );
        }
        $this->workflowManager->massStartWorkflow($massStartData);

        return self::SUCCESS;
    }
}
