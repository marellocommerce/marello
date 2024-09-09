<?php

namespace Marello\Bundle\TaxBundle\Provider;

use Symfony\Contracts\Translation\TranslatorInterface;

use Marello\Bundle\TaxBundle\Model\Result;
use Marello\Bundle\TaxBundle\Model\Taxable;
use Marello\Bundle\TaxBundle\Factory\TaxFactory;
use Marello\Bundle\TaxBundle\Event\TaxEventDispatcher;
use Marello\Bundle\PricingBundle\Subtotal\Model\Subtotal;
use Marello\Bundle\PricingBundle\Subtotal\Provider\SubtotalProviderInterface;

class TaxSubtotalProvider implements SubtotalProviderInterface
{
    const TYPE = 'tax';
    const NAME = 'marello_tax.subtotal_tax';
    const SUBTOTAL_ORDER = 50;

    /**
     * @param TranslatorInterface $translator
     * @param TaxEventDispatcher $eventDispatcher
     * @param TaxFactory $taxFactory
     */
    public function __construct(
        protected TranslatorInterface $translator,
        protected TaxEventDispatcher $eventDispatcher,
        protected TaxFactory $taxFactory,
        protected CompanyReverseTaxProvider $provider
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotal($entity)
    {
        $subtotal = $this->createSubtotal();

        try {
            $tax = $this->getTax($entity);
            $this->fillSubtotal($subtotal, $tax, $entity);
        } catch (\Exception $e) {
        }

        return $subtotal;
    }

    /**
     * @return Subtotal
     */
    protected function createSubtotal()
    {
        $subtotal = new Subtotal([]);

        $subtotal->setType(self::TYPE);
        $label = sprintf('marello.tax.subtotals.%s.label', self::TYPE);
        $subtotal->setLabel($this->translator->trans($label));
        $subtotal->setSortOrder(self::SUBTOTAL_ORDER);

        return $subtotal;
    }

    /**
     * @param Subtotal $subtotal
     * @param Result $tax
     * @return Subtotal
     */
    protected function fillSubtotal(Subtotal $subtotal, Result $tax, $entity)
    {
        $taxAmount = $tax->getTotal()->getTaxAmount();
        if (!$this->provider->orderIsTaxable($entity)) {
            $taxAmount = 0;
        }

        $subtotal->setAmount($taxAmount);
        $subtotal->setCurrency($tax->getTotal()->getCurrency());
        $subtotal->setVisible((bool)$tax->getTotal()->getTaxAmount());

        return $subtotal;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($entity)
    {
        return $this->taxFactory->supports($entity);
    }

    /**
     * @param object $object
     * @return Result
     */
    public function getTax($object)
    {
        return $this->getTaxable($object)->getResult();
    }

    /**
     * @param object $object
     * @return Taxable
     */
    protected function getTaxable($object)
    {
        $taxable = $this->taxFactory->create($object);
        $taxable->setResult(new Result());

        $this->eventDispatcher->dispatch($taxable);

        return $taxable;
    }
}
