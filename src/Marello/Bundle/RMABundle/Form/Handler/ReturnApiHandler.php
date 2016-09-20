<?php

namespace Marello\Bundle\RMABundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Marello\Component\RMA\Model\ReturnEntityInterface;
use Oro\Bundle\SoapBundle\Controller\Api\FormAwareInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ReturnApiHandler implements FormAwareInterface
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @param FormInterface $form
     * @param Request       $request
     * @param ObjectManager $manager
     */
    public function __construct(FormInterface $form, Request $request, ObjectManager $manager)
    {
        $this->form    = $form;
        $this->request = $request;
        $this->manager = $manager;
    }

    /**
     * Process form
     *
     * @param  ReturnEntityInterface $entity
     *
     * @return bool True on successful processing, false otherwise
     */
    public function process(ReturnEntityInterface $entity)
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
     * "Success" form handler
     *
     * @param ReturnEntityInterface $entity
     */
    protected function onSuccess(ReturnEntityInterface $entity)
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }
}
