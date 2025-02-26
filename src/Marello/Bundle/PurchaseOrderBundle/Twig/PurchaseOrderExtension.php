<?php

namespace Marello\Bundle\PurchaseOrderBundle\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

use Symfony\Component\Intl\Currencies;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\WorkflowBundle\Model\WorkflowManager;

use Marello\Bundle\SupplierBundle\Entity\Supplier;
use Marello\Bundle\PurchaseOrderBundle\Entity\PurchaseOrder;

class PurchaseOrderExtension extends AbstractExtension
{
    const NAME = 'marello_purchaseorder';
    
    /** @var WorkflowManager $workflowManager */
    protected $workflowManager;

    /** @var DoctrineHelper $doctrineHelper */
    protected $doctrineHelper;

    /**
     * ProductExtension constructor.
     *
     * @param WorkflowManager $workflowManager
     */
    public function __construct(WorkflowManager $workflowManager)
    {
        $this->workflowManager = $workflowManager;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'marello_purchaseorder_can_edit',
                [$this, 'canEdit']
            ),
            new TwigFunction(
                'marello_get_supplier_currency',
                [$this, 'getSupplierCurrency']
            ),
            new TwigFunction(
                'marello_get_supplier_currency_symbol',
                [$this, 'getSupplierCurrencySymbol']
            )
        ];
    }

    /**
     * @param PurchaseOrder $purchaseOrder
     *
     * @return boolean
     */
    public function canEdit(PurchaseOrder $purchaseOrder)
    {
        $workflowItems = $this->workflowManager->getWorkflowItemsByEntity($purchaseOrder);
        foreach ($workflowItems as $workflowItem) {
            if ('not_sent' === $workflowItem->getCurrentStep()->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int|null $supplierId
     * @return string|null
     */
    public function getSupplierCurrency(?int $supplierId)
    {
        if (!$supplierId) {
            return null;
        }

        $supplier = $this->doctrineHelper->getEntity(Supplier::class, $supplierId);
        if (!$supplier) {
            return null;
        }

        return $supplier->getCurrency();
    }

    /**
     * @param int|null $supplierId
     * @return string|null
     */
    public function getSupplierCurrencySymbol(?int $supplierId)
    {
        if (!$supplierId) {
            return null;
        }

        $supplier = $this->doctrineHelper->getEntity(Supplier::class, $supplierId);
        if (!$supplier) {
            return null;
        }

        return Currencies::getSymbol($supplier->getCurrency());
    }

    public function setDoctrineHelper(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }
}
