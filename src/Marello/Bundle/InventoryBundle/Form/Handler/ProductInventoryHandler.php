<?php

namespace Marello\Bundle\InventoryBundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Marello\Component\Inventory\Logging\InventoryLoggerInterface;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Component\Product\ProductInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductInventoryHandler
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
     * @param FormInterface            $form
     * @param Request                  $request
     * @param ObjectManager            $manager
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
                $this->onSuccess($entity);

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
     */
    protected function onSuccess(ProductInterface $entity)
    {
        $items = $entity->getInventoryItems()->toArray();

        foreach ($entity->getVariant()->getProducts() as $product) {
            $items = array_merge($items, $product->getInventoryItems()->toArray());
        }

        $this->inventoryLogger->log($items, 'manual');

        $this->manager->persist($entity->getVariant());
        $this->manager->flush();
    }
}
