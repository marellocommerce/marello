<?php

namespace Marello\Bundle\CustomerBundle\Form\Type;

class CustomerAwareCompanySelectType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_customer_aware_company_select';

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'autocomplete_alias' => 'marello_customer_companies',
                'grid_name'          => 'marello-customer-companies-select-grid',
                'attr' => [
                    'class' => 'marello-customer-aware-company-select',
                ],
                'customer_id' => null,
            ]
        );
    }

}