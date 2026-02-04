<?php

namespace Marello\Bundle\InventoryBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\FormBundle\Form\Handler\RequestHandlerTrait;
use Oro\Bundle\FormBundle\Form\Handler\FormHandlerInterface;

use Marello\Bundle\InventoryBundle\Entity\DeliveryPromise;

class DeliveryPromiseHandler implements FormHandlerInterface
{
    use RequestHandlerTrait;

    public function __construct(
        protected DoctrineHelper $doctrineHelper
    ) {
    }

    /**
     * @param $data
     * @param FormInterface $form
     * @param Request $request
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function process($data, FormInterface $form, Request $request)
    {
        if (!$data instanceof DeliveryPromise) {
            throw new \InvalidArgumentException('Argument data should be instance of Order entity');
        }

        $form->setData($data);

        if (in_array($request->getMethod(), ['POST', 'PUT'])) {
            $this->submitPostPutRequest($form, $request);
            if ($form->isValid()) {
                $this->onSuccess($data);

                return true;
            }
        }

        return false;
    }

    /**
     * "Success" form handler
     *
     * @param DeliveryPromise $entity
     */
    protected function onSuccess(
        DeliveryPromise $entity,
    ) {
        $entity->updateDenormalizedProperties();
        $em = $this->doctrineHelper->getEntityManagerForClass(DeliveryPromise::class);
        $em->persist($entity);
        $em->flush();
    }
}
