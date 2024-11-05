<?php

namespace Marello\Bundle\PaymentBundle\Method\EventListener;

use Psr\Log\LoggerInterface;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;

use Marello\Bundle\PaymentBundle\Entity\PaymentMethodConfig;
use Marello\Bundle\PaymentBundle\Method\Event\MethodRemovalEvent;
use Marello\Bundle\PaymentBundle\Entity\PaymentMethodsConfigsRule;
use Marello\Bundle\PaymentBundle\Entity\Repository\PaymentMethodConfigRepository;
use Marello\Bundle\PaymentBundle\Entity\Repository\PaymentMethodsConfigsRuleRepository;

class MethodRemovalListener
{
    /**
     * @var DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param DoctrineHelper $doctrineHelper
     * @param LoggerInterface $logger
     */
    public function __construct(DoctrineHelper $doctrineHelper, LoggerInterface $logger)
    {
        $this->doctrineHelper = $doctrineHelper;
        $this->logger = $logger;
    }

    /**
     * @param MethodRemovalEvent $event
     * @throws \Exception
     */
    public function onMethodRemove(MethodRemovalEvent $event)
    {
        $methodId = $event->getMethodIdentifier();
        $connection = $this->getEntityManager()->getConnection();
        try {
            $connection->beginTransaction();
            $this->getPaymentMethodConfigRepository()->deleteByMethod($methodId);
            $this->getPaymentMethodsConfigsRuleRepository()->disableRulesWithoutPaymentMethods();
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            $this->logger->critical($e->getMessage(), [
                'payment_method_identifier' => $methodId
            ]);
        }
    }

    /**
     * @return \Doctrine\ORM\EntityManager|null
     */
    private function getEntityManager()
    {
        return $this->doctrineHelper->getEntityManagerForClass(PaymentMethodsConfigsRule::class);
    }

    /**
     * @return PaymentMethodConfigRepository|\Doctrine\ORM\EntityRepository
     */
    private function getPaymentMethodConfigRepository()
    {
        return $this->doctrineHelper->getEntityRepository(PaymentMethodConfig::class);
    }

    /**
     * @return PaymentMethodsConfigsRuleRepository|\Doctrine\ORM\EntityRepository
     */
    private function getPaymentMethodsConfigsRuleRepository()
    {
        return $this->doctrineHelper->getEntityRepository(PaymentMethodsConfigsRule::class);
    }
}
