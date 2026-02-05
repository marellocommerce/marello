<?php

namespace Marello\Bundle\InvoiceBundle\Form\Type;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Bundle\FormBundle\Form\Type\CheckboxType as BaseCheckboxType;

use Marello\Bundle\CustomerBundle\Entity\Company;
use Marello\Bundle\CustomerBundle\Entity\Customer;

class CompanyEmailCheckboxType extends AbstractType
{
    /**
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'relatedEntity' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $showField = false;
        if (isset($options['relatedEntity'])) {
            $entity = $options['relatedEntity'];
            $showField = $this->getIsShowField($entity);
        }

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($showField) {
                $form = $event->getForm()->getParent();
                if ($form->has('send_company_email') && !$showField) {
                    $form->remove('send_company_email');
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['show_field'] = false;
        if (isset($options['relatedEntity'])) {
            $entity = $options['relatedEntity'];
            $view->vars['show_field'] = $this->getIsShowField($entity);
        }
    }

    /**
     * @param $entity
     * @return bool
     */
    protected function getIsShowField($entity): bool
    {
        $showField = false;
        if ($entity instanceof Customer) {
            if ($entity->getCompany()) {
                $showField = (bool)$entity->getCompany()?->getInvoiceEmail();
            }
        } elseif ($entity instanceof Company) {
            $showField = (bool)$entity->getInvoiceEmail();
        }

        return $showField;
    }

    public function getParent()
    {
        return BaseCheckboxType::class;
    }

    public function getBlockPrefix()
    {
        return 'invoice_email_checkbox';
    }
}
