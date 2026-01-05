<?php

namespace Marello\Bundle\CustomerBundle\Form\Handler;

use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Marello\Bundle\CustomerBundle\Entity\Company;
use Marello\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\FormBundle\Form\Handler\RequestHandlerTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use Oro\Bundle\FormBundle\Form\Handler\FormHandlerInterface;
class CompanyHandler implements FormHandlerInterface
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
     * @var EntityManagerInterface
     */
    protected $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Company $company
     *
     * @return bool True on successful processing, false otherwise
     */
    public function process($data, FormInterface $form, Request $request)
    {
        if (!$data instanceof Company) {
            throw new \InvalidArgumentException('Argument data should be instance of Customer entity');
        }

        $form->setData($data);

        if (in_array($request->getMethod(), ['POST', 'PUT'])) {
            $this->submitPostPutRequest($form, $request);

            if ($form->isValid()) {
                $appendCustomers = $form->get('appendCustomers');
                /** @var FormInterface $removeCustomers */
                $removeCustomers = $form->get('removeCustomers');
                $this->onSuccess($data, $appendCustomers->getData(), $removeCustomers->getData());

                return true;
            }
        }

        return false;
    }

    /**
     * @param Company $company
     * @param Customer[] $appendCustomers
     * @param Customer[] $removeCustomers
     */
    protected function onSuccess(Company $company, array $appendCustomers, array $removeCustomers)
    {
        $this->appendCustomers($company, $appendCustomers);
        $this->removeCustomers($company, $removeCustomers);

        $this->manager->persist($company);
        $this->manager->flush();
    }

    /**
     * @param Company $company
     * @param Customer[] $customers
     */
    protected function appendCustomers(Company $company, array $customers)
    {
        /** @var $customer Customer */
        foreach ($customers as $customer) {
            $company->addCustomer($customer);
        }
    }

    /**
     * @param Company $company
     * @param Customer[] $customers
     */
    protected function removeCustomers(Company $company, array $customers)
    {
        /** @var $customer Customer */
        foreach ($customers as $customer) {
            $company->removeCustomer($customer);
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
