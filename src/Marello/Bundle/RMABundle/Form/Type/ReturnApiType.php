<?php

namespace Marello\Bundle\RMABundle\Form\Type;

use Marello\Bundle\RMABundle\Form\DataTransformer\OrderToOrderNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class ReturnApiType extends AbstractType
{
    const NAME = 'marello_return_api';

    /** @var OrderToOrderNumberTransformer */
    protected $orderToOrderNumberTransformer;

    /**
     * ReturnApiType constructor.
     *
     * @param OrderToOrderNumberTransformer $orderToOrderNumberTransformer
     */
    public function __construct(OrderToOrderNumberTransformer $orderToOrderNumberTransformer)
    {
        $this->orderToOrderNumberTransformer = $orderToOrderNumberTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('order', 'text', [
                'required'    => true,
                'constraints' => new NotNull(),
            ])
            ->add('returnNumber', 'text', [
                'required' => false,
            ])
            ->add('returnItems', 'collection', [
                'type'         => ReturnItemApiType::NAME,
                'allow_add'    => true,
                'by_reference' => false,
            ]);

        $builder->get('order')->addModelTransformer($this->orderToOrderNumberTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'      => 'Marello\Component\RMA\Entity\ReturnEntity',
            'csrf_protection' => false,
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return self::NAME;
    }
}
