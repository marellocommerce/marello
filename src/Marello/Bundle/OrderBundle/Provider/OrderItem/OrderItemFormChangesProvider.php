<?php

namespace Marello\Bundle\OrderBundle\Provider\OrderItem;

use Symfony\Contracts\Translation\TranslatorInterface;

use Marello\Bundle\LayoutBundle\Context\FormChangeContextInterface;
use Marello\Bundle\LayoutBundle\Provider\FormChangesProviderInterface;

class OrderItemFormChangesProvider extends AbstractOrderItemFormChangesProvider
{
    /**
     * @var FormChangesProviderInterface[]
     */
    protected $providers = [];

    /**
     * @var TranslatorInterface $translator
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $identifier
     * @param FormChangesProviderInterface $provider
     */
    public function addProvider($identifier, FormChangesProviderInterface $provider)
    {
        $this->providers[$identifier] = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function processFormChanges(FormChangeContextInterface $context)
    {
        $submittedData = $context->getSubmittedData();
        if ($context->getForm()->has(self::ITEMS_FIELD) &&
            array_key_exists(self::CHANNEL_FIELD, $submittedData) &&
            array_key_exists(self::ITEMS_FIELD, $submittedData)) {
            foreach ($this->providers as $field => $provider) {
                $provider->processFormChanges($context);
            }

            $itemsCount = count($submittedData[self::ITEMS_FIELD]);
            $result = $context->getResult();
            $itemResult = [];
            foreach ($result[self::ITEMS_FIELD] as $field => $dataByIdentifier) {
                $resultCount = count($dataByIdentifier);
                foreach ($dataByIdentifier as $identifier => $data) {
                    $itemResult[$identifier][$field] = $data;
                }

                if ($itemsCount !== $resultCount) {
                    $productIds = $this->getProductIdsFromSubmittedData($submittedData);
                    foreach ($productIds as $product) {
                        if (!array_key_exists(self::IDENTIFIER_PREFIX . $product, $itemResult) ||
                            !array_key_exists($field, $itemResult[self::IDENTIFIER_PREFIX . $product])) {
                            $itemResult[self::IDENTIFIER_PREFIX . $product] = [
                                'message' => $this->translator
                                    ->trans('marello.order.orderitem.messages.product_not_salable'),
                            ];
                        }
                    }
                }
            }

            $duplicatedItems = array_diff_assoc($productIds, array_unique($productIds));
            if(!empty($duplicatedItems)) {
                foreach ($duplicatedItems as $duplicatedItem)
                {
                    $itemResult[self::IDENTIFIER_PREFIX . $duplicatedItem] = [
                        'message' => $this->translator
                            ->trans('marello.order.orderitem.messages.product_duplicated_in_order'),
                    ];
                }
            }

            $result[self::ITEMS_FIELD] = $itemResult;
            $context->setResult($result);
        }
    }

    /**
     * Get product ids from the submitted data
     * @param $data
     * @return array
     */
    protected function getProductIdsFromSubmittedData($data)
    {
        $productIds = [];
        foreach ($data[self::ITEMS_FIELD] as $item) {
            $productIds[] = (int)$item['product'];
        }

        return $productIds;
    }
}
