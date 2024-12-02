<?php

namespace Marello\Bundle\CustomerBundle\Api\Processor;

use Oro\Bundle\ApiBundle\Processor\SingleItemContext;

use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;

/**
 * lower cases the email ID for searching single customer
 */
class SetLowerCaseEmailId implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContextInterface $context): void
    {
        /** @var SingleItemContext $context */
        $customerEmail = $context->getId();
        if (null !== $customerEmail) {
            $context->setId(mb_strtolower($customerEmail));
        }
    }
}
