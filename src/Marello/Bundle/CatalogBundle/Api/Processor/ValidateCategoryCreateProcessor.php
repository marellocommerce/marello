<?php

namespace Marello\Bundle\CatalogBundle\Api\Processor;

use Doctrine\ORM\EntityManagerInterface;
use Marello\Bundle\CustomerBundle\Entity\Customer;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;
use Oro\Bundle\ApiBundle\Processor\Create\CreateContext;
use Oro\Bundle\ApiBundle\Exception\RuntimeException;

class ValidateCategoryCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContextInterface $context): void
    {
        /** @var CreateContext $context */
        $data = $context->getRequestData();

        if (!isset($data['data']) || !is_array($data['data'])) {
            throw new RuntimeException('The request must contain a "data" section.');
        }

        $resource = $data['data'];

        if ($resource['attributes']['categoryType'] == 'customer') {
            if (empty($resource['relationships']['customer']) || $resource['relationships']['customer']['data'] === null) {
                throw new RuntimeException('Customer relationship is required for "customer" category type.');
            }

            if (!array_key_exists('isPersonal', $resource['attributes'])) {
                throw new RuntimeException('The request must contain "isPersonal" attribute for "customer" category type.');
            }

            if (!$resource['attributes']['isPersonal'] && !$this->hasCompany($resource['relationships']['customer']['data']['id'])) {
                throw new RuntimeException('Customer must have Company to create personal "customer" category type.');
            }
        }
    }

    protected function hasCompany(string $customerId): bool
    {
        $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(['email' => $customerId]);

        if ($customer->getCompany() !== null) {
            return true;
        }

        return false;
    }
}