<?php

namespace Marello\Bundle\SalesBundle\Form\Handler;

use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Oro\Bundle\FormBundle\Form\Handler\RequestHandlerTrait;
use Oro\Bundle\FormBundle\Form\Handler\FormHandlerInterface;

use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\SalesBundle\Entity\SalesChannelGroup;

class SalesChannelHandler implements FormHandlerInterface
{
    use RequestHandlerTrait;

    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    /**
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function process($data, FormInterface $form, Request $request)
    {
        if (!$data instanceof SalesChannel) {
            throw new \InvalidArgumentException('Argument data should be instance of SalesChannel entity');
        }
        
        $form->setData($data);

        if (in_array($request->getMethod(), ['POST', 'PUT'])) {
            $this->submitPostPutRequest($form, $request);
            $selectedSalesChannelGroup = null;
            if ($form->has('selectSalesChannelGroup')) {
                $selectedSalesChannelGroup = $form->get('selectSalesChannelGroup')->getData();
            }

            if ($form->isValid()) {
                $this->onSuccess($data, $selectedSalesChannelGroup);

                return true;
            }
        }

        return false;
    }

    /**
     * "Success" form handler
     * @param SalesChannel $entity
     * @param SalesChannelGroup|null $salesChannelGroup
     * @return void
     */
    protected function onSuccess(SalesChannel $entity, ?SalesChannelGroup $salesChannelGroup)
    {
        $entity->setGroup($salesChannelGroup);
        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
