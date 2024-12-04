<?php

namespace Marello\Bundle\PaymentBundle\Action\Handler;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\PaymentBundle\Entity\Payment;
use Marello\Bundle\InvoiceBundle\Entity\Invoice;
use Marello\Bundle\InvoiceBundle\Entity\AbstractInvoice;
use Marello\Bundle\InvoiceBundle\Provider\InvoicePaidAmountProvider;
use Marello\Bundle\PaymentBundle\Migrations\Data\ORM\LoadPaymentStatusData;

class AddPaymentActionHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var InvoicePaidAmountProvider
     */
    private $invoicePaidAmountProvider;

    /** @var DoctrineHelper $doctrineHelper */
    private $doctrineHelper;

    /**
     * @param EntityManagerInterface $entityManager
     * @param InvoicePaidAmountProvider $invoicePaidAmountProvider
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        InvoicePaidAmountProvider $invoicePaidAmountProvider
    ) {
        $this->entityManager = $entityManager;
        $this->invoicePaidAmountProvider = $invoicePaidAmountProvider;
    }

    /**
     * @param AbstractInvoice $entity
     * @param string $paymentMethod
     * @param \DateTime $paymentDate
     * @param string $paymentReference
     * @param string $paymentDetails
     * @param float $paidTotal
     * @return array
     */
    public function handleAction(
        AbstractInvoice $entity,
        $paymentMethod,
        \DateTime $paymentDate,
        $paymentReference,
        $paymentDetails,
        $paidTotal
    ) {
        $paidTotalBefore = $this->invoicePaidAmountProvider->getPaidAmount($entity);
        $paidTotalAfter = $paidTotalBefore + $paidTotal;
        if ($paidTotalAfter > $entity->getGrandTotal()) {
            return [
                'type' => 'error',
                'message' => 'marello.payment.message.add_payment.error.paid_total_exceed'
            ];
        }
        $payment = new Payment();
        $payment
            ->setPaymentMethod($paymentMethod)
            ->setPaymentDate($paymentDate)
            ->setPaymentReference($paymentReference)
            ->setPaymentDetails($paymentDetails)
            ->setTotalPaid($paidTotal)
            ->setCurrency($entity->getCurrency())
            ->setStatus($this->findStatusByName(LoadPaymentStatusData::ASSIGNED))
            ->setOrganization($entity->getOrganization());
        $order = $entity->getOrder();
        if ($order && $paymentMethod === $order->getPaymentMethod()) {
            $payment->setPaymentMethodOptions($order->getPaymentMethodOptions());
        }
        $entity->addPayment($payment);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return [
            'type' => 'success',
            'message' => 'marello.payment.message.add_payment.success'
        ];
    }

    /**
     * @param object $entity
     * @param $paymentMethod
     * @param \DateTime $paymentDate
     * @param $paymentReference
     * @param $paymentDetails
     * @param $paidTotal
     * @return array|string[]
     */
    public function addPaymentToEntity(
        object $entity,
        $paymentMethod,
        \DateTime $paymentDate,
        $paymentReference,
        $paymentDetails,
        $paidTotal
    ): array {
        if ($entity instanceof Order) {
            $repo = $this->doctrineHelper->getEntityRepositoryForClass(AbstractInvoice::class);
            $invoices = $repo->findBy([
                'order' => $entity->getId(),
                'invoiceType' => Invoice::INVOICE_TYPE
            ]);

            if (!empty($invoices)) {
                $invoice = array_shift($invoices);
                // only allow to add on the first invoice that is found, at least for now.
                return $this->handleAction(
                    $invoice,
                    $paymentMethod,
                    $paymentDate,
                    $paymentReference,
                    $paymentDetails,
                    $paidTotal
                );
            }
        }

        if ($entity instanceof AbstractInvoice) {
            return $this->handleAction(
                $entity,
                $paymentMethod,
                $paymentDate,
                $paymentReference,
                $paymentDetails,
                $paidTotal
            );
        }

        return [
            'type' => 'error',
            'message' => 'marello.payment.message.add_payment.error.entity_not_supported',
            'message_parameters' => [
                'entity_type' => ClassUtils::getClass($entity)
            ]
        ];
    }

    /**
     * @param string $name
     * @return null|object
     */
    private function findStatusByName($name): ?object
    {
        $statusClass = ExtendHelper::buildEnumValueClassName(
            LoadPaymentStatusData::PAYMENT_STATUS_ENUM_CLASS
        );
        $status = $this->entityManager
            ->getRepository($statusClass)
            ->find($name);

        if ($status) {
            return $status;
        }

        return null;
    }

    /**
     * @param DoctrineHelper $doctrineHelper
     * @return void
     */
    public function setDoctrineHelper(DoctrineHelper $doctrineHelper): void
    {
        $this->doctrineHelper = $doctrineHelper;
    }
}
