<?php

namespace Marello\Bundle\ProductBundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Marello\Component\Inventory\Logging\InventoryLoggerInterface;
use Marello\Component\Sales\Entity\SalesChannel;
use Marello\Component\Product\Model\ProductInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductHandler
{
    /** @var FormInterface */
    protected $form;

    /** @var Request */
    protected $request;

    /** @var ObjectManager */
    protected $manager;

    /** @var InventoryLoggerInterface */
    protected $inventoryLogger;

    /**
     * @param FormInterface   $form
     * @param Request         $request
     * @param ObjectManager   $manager
     * @param InventoryLoggerInterface $inventoryLogger
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        ObjectManager $manager,
        InventoryLoggerInterface $inventoryLogger
    ) {
        $this->form            = $form;
        $this->request         = $request;
        $this->manager         = $manager;
        $this->inventoryLogger = $inventoryLogger;
    }

    /**
     * Process form
     *
     * @param  ProductInterface $entity
     *
     * @return bool True on successful processing, false otherwise
     */
    public function process(ProductInterface $entity)
    {
        $this->form->setData($entity);

        if (in_array($this->request->getMethod(), ['POST', 'PUT'])) {
            $this->form->submit($this->request);

            if ($this->form->isValid()) {
                $addChannels = $this->form->get('addSalesChannels')->getData();
                $removeChannels = $this->form->get('removeSalesChannels')->getData();
                $this->onSuccess($entity, $addChannels, $removeChannels);

                return true;
            }
        }

        return false;
    }

    /**
     * Returns form instance
     *
     * @return FormInterface
     */
    public function getFormView()
    {
        return $this->form->createView();
    }

    /**
     * "Success" form handler
     *
     * @param ProductInterface $entity
     * @param array $addChannels
     * @param array $removeChannels
     */
    protected function onSuccess(ProductInterface $entity, array $addChannels, array $removeChannels)
    {
        $this->addChannels($entity, $addChannels);
        $this->removeChannels($entity, $removeChannels);
        $this->inventoryLogger->log($entity->getInventoryItems()->toArray(), 'manual');

        $this->manager->persist($entity);
        $this->manager->flush();
    }

    /**
     * Add channels to product
     *
     * @param ProductInterface $product
     * @param SalesChannel[] $channels
     */
    protected function addChannels(ProductInterface $product, array $channels)
    {
        /** @var $channel SalesChannel */
        foreach ($channels as $channel) {
            $product->addChannel($channel);
        }
    }

    /**
     * Remove channels from product
     *
     * @param ProductInterface $product
     * @param SalesChannel[] $channels
     */
    protected function removeChannels(ProductInterface $product, array $channels)
    {
        /** @var $channels SalesChannel */
        foreach ($channels as $channel) {
            $product->removeChannel($channel);
        }
    }
}
