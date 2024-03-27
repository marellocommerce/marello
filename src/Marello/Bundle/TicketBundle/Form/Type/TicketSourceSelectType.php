<?php

namespace Marello\Bundle\TicketBundle\Form\Type;

use Marello\Bundle\TicketBundle\Provider\TicketSourceInterface;
use Oro\Bundle\EntityExtendBundle\Form\Type\EnumSelectType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class TicketSourceSelectType extends EnumSelectType
{
    const BLOCK_PREFIX = 'marello_ticket_source_select';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'enum_code' => TicketSourceInterface::TICKET_SOURCE_ENUM_CODE,
            'configs' => ['allowClear' => false],
            'constraints' => [new NotNull()],
        ]);
    }

    protected function getNewEntityFromNearestParentForm(FormInterface $form)
    {
        $parent = $form->getParent();
        if (!$parent) {
            return null;
        }

        if ($parent->getConfig()->getOption('data_class')) {
            $data = $parent->getData();
            if ($data && is_object($data) && method_exists($data, 'getId')) {
                return $data;
            }
            return null;
        }

        return $this->getNewEntityFromNearestParentForm($parent);
    }

    protected function isDataEmptyValue($targetEntity, FormInterface $form)
    {
        return true;
    }

    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}