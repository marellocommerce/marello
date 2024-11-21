<?php

namespace Marello\Bundle\CustomerBundle\Form\Handler;

use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Marello\Bundle\CustomerBundle\Entity\Customer;
use Marello\Bundle\CustomerBundle\Entity\CustomerGroup;
use Oro\Bundle\FormBundle\Form\Handler\RequestHandlerTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CustomerGroupHandler
{
    use RequestHandlerTrait;

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @param FormInterface $form
     * @param RequestStack  $requestStack
     * @param ObjectManager $manager
     */
    public function __construct(
        FormInterface $form,
        RequestStack  $requestStack,
        ObjectManager $manager
    ) {
        $this->form = $form;
        $this->request = $requestStack->getCurrentRequest();
        $this->manager = $manager;
    }

    /**
     * @param CustomerGroup $customerGroup
     *
     * @return bool True on successful processing, false otherwise
     */
    public function process(CustomerGroup $customerGroup)
    {
        $this->form->setData($customerGroup);

        if (in_array($this->request->getMethod(), ['POST', 'PUT'])) {
            $this->submitPostPutRequest($this->form, $this->request);
            if ($this->form->isValid()) {
                /** @var FormInterface $appendCustomers */
                $appendCustomers = $this->form->get('appendCustomers');
                /** @var FormInterface $removeCustomers */
                $removeCustomers = $this->form->get('removeCustomers');
                $this->onSuccess($customerGroup, $appendCustomers->getData(), $removeCustomers->getData());

                return true;
            }
        }

        return false;
    }

    /**
     * @param CustomerGroup $customerGroup
     * @param Customer[] $appendCustomers
     * @param Customer[] $removeCustomers
     */
    protected function onSuccess(CustomerGroup $customerGroup, array $appendCustomers, array $removeCustomers)
    {
        $this->appendCustomers($customerGroup, $appendCustomers);
        $this->removeCustomers($customerGroup, $removeCustomers);

        $this->manager->persist($customerGroup);
        $this->manager->flush();
    }

    /**
     * @param CustomerGroup $customerGroup
     * @param Customer[] $customers
     */
    protected function appendCustomers(CustomerGroup $customerGroup, array $customers)
    {
        /** @var $customer Customer */
        foreach ($customers as $customer) {
            $customerGroup->addCustomer($customer);
        }
    }

    /**
     * @param CustomerGroup $customerGroup
     * @param Customer[] $customers
     */
    protected function removeCustomers(CustomerGroup $customerGroup, array $customers)
    {
        /** @var $customer Customer */
        foreach ($customers as $customer) {
            $customerGroup->removeCustomer($customer);
        }
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
}
