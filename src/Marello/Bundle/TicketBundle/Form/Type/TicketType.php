<?php

namespace Marello\Bundle\TicketBundle\Form\Type;

use Marello\Bundle\CustomerBundle\Form\Type\CustomerSelectType;
use Marello\Bundle\TicketBundle\Entity\Ticket;
use Marello\Bundle\TicketBundle\Provider\TicketPriorityInterface;
use Marello\Bundle\TicketBundle\Provider\TicketSourceInterface;
use Oro\Bundle\EntityConfigBundle\Config\Id\EntityConfigId;
use Oro\Bundle\EntityExtendBundle\Form\Type\EnumChoiceType;
use Oro\Bundle\UserBundle\Form\Type\UserSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for Ticket entity.
 */
class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'customer',
                CustomerSelectType::class,
                ['label' => 'marello.ticket.customer.label', 'required' => false]
            )
            ->add(
                'firstName',
                TextType::class,
                ['label' => 'marello.ticket.first_name.label', 'required' => true]
            )
            ->add(
                'lastName',
                TextType::class,
                ['label' => 'marello.ticket.last_name.label', 'required' => true]
            )
            ->add(
                'email',
                TextType::class,
                ['label' => 'marello.ticket.email.label', 'required' => true]
            )
            ->add(
                'phone',
                TextType::class,
                ['label' => 'marello.ticket.phone.label', 'required' => false]
            )
            ->add(
                'owner',
                UserSelectType::class,
                ['label' => 'marello.ticket.owner.label', 'required' => true]
            )
            ->add(
                'assignedTo',
                UserSelectType::class,
                ['label' => 'marello.ticket.assigned_to.label', 'required' => false]
            )
            ->add(
                'source',
                EnumChoiceType::class,
                [
                    'enum_code' => TicketSourceInterface::TICKET_SOURCE_ENUM_CODE,
                    'label' => 'marello.ticket.source.label',
                    'required' => true]
            )
            ->add(
                'priority',
                EnumChoiceType::class,
                [
                    'enum_code' => TicketPriorityInterface::TICKET_PRIORITY_ENUM_CODE,
                    'label' => 'marello.ticket.priority.label',
                    'required' => true]
            )
            // Add category type for form ?
            ->add(
                'category',
                TextType::class,
                ['label' => 'marello.ticket.category.label', 'required' => true]
            )
            ->add(
                'subject',
                TextType::class,
                ['label' => 'marello.ticket.subject.label', 'required' => true]
            )
            ->add(
                'description',
                TextType::class,
                ['label' => 'marello.ticket.description.label', 'required' => true]
            )
            ->add(
                'resolution',
                TextType::class,
                [
                    'label' => 'marello.ticket.resolution.label',
                    'required' => false
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class
        ]);
    }
}