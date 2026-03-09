<?php

namespace Marello\Bundle\CoreBundle\Form\Extension;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Oro\Bundle\EmailBundle\Form\Type\EmailTemplateType;

/**
 * Reorganizes form fields on the product attribute configuration page
 */
class EmailTemplateExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // disable some fields for non editable email template
        $setDisabled = function (&$options) {
            if (isset($options['auto_initialize'])) {
                $options['auto_initialize'] = false;
            }
            $options['disabled'] = true;
            $options['label'] = 'marello.core.emailtemplate.entity.name.label';
        };
        $factory = $builder->getFormFactory();
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($factory, $setDisabled) {
                $data = $event->getData();
                if ($data && $data->getId()) {
                    $form = $event->getForm();
                    $options = $form->get('name')->getConfig()->getOptions();
                    $setDisabled($options);
                    $form->add($factory->createNamed('name', TextType::class, null, $options));
                }
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [EmailTemplateType::class];
    }
}
