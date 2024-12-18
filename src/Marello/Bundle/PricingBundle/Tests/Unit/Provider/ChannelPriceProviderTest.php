<?php

namespace Marello\Bundle\PricingBundle\Tests\Unit\Provider;

use Doctrine\ORM\EntityRepository;
use Marello\Bundle\LayoutBundle\Context\FormChangeContext;
use Marello\Bundle\LayoutBundle\Context\FormChangeContextInterface;
use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\OrderBundle\Provider\OrderItem\OrderItemFormChangesProvider;
use Marello\Bundle\PricingBundle\Entity\AssembledChannelPriceList;
use Marello\Bundle\PricingBundle\Entity\AssembledPriceList;
use Marello\Bundle\PricingBundle\Entity\ProductChannelPrice;
use Marello\Bundle\PricingBundle\Entity\ProductPrice;
use Marello\Bundle\PricingBundle\Provider\ChannelPriceProvider;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\Entity\Repository\ProductRepository;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Oro\Bundle\EntityBundle\ORM\Registry;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Component\Testing\Unit\EntityTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;

class ChannelPriceProviderTest extends TestCase
{
    use EntityTrait;

    /**
     * @var Registry|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $registry;

    /**
     * @var AclHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $aclHelper;

    /**
     * @var FormChangeContextInterface
     */
    protected $context;

    /**
     * @var ChannelPriceProvider
     */
    protected $channelPriceProvider;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(Registry::class);
        $this->aclHelper = $this->createMock(AclHelper::class);
        $this->channelPriceProvider = new ChannelPriceProvider($this->registry, $this->aclHelper);
    }

    /**
     * @dataProvider processFormChangesDataProvider
     *
     * @param AssembledChannelPriceList $assembledChannelPriceList
     * @param AssembledPriceList $assembledPriceList
     * @param int $expectedValue
     */
    public function testProcessFormChanges(
        AssembledChannelPriceList $assembledChannelPriceList = null,
        AssembledPriceList $assembledPriceList = null,
        $expectedValue
    ) {
        /** @var SalesChannel $channel */
        $channel = $this->getEntity(SalesChannel::class, ['id' => 1, 'currency' => 'EUR']);
        /** @var Order $order */
        $order = $this->getEntity(Order::class, ['id' => 1, 'salesChannel' => $channel]);
        /** @var Product $product */
        $product = $this->getEntity(Product::class, ['id' => 1]);

        /** @var FormInterface|\PHPUnit\Framework\MockObject\MockObject $form **/
        $form = $this->createMock(FormInterface::class);
        $form->expects(static::once())
            ->method('getData')
            ->willReturn($order);

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository
            ->expects(static::once())
            ->method('findBySalesChannel')
            ->with($channel->getId(), [$product->getId()])
            ->willReturn([$product]);

        $productPriceRepository = $this->createMock(EntityRepository::class);
        $productPriceRepository
            ->expects(static::once())
            ->method('findOneBy')
            ->with(['product' => $product->getId(), 'currency' => $channel->getCurrency()])
            ->willReturn($assembledPriceList);

        $productChannelPriceRepository = $this->createMock(EntityRepository::class);
        $productChannelPriceRepository
            ->expects(static::once())
            ->method('findOneBy')
            ->with([
                'channel' => $channel->getId(),
                'product' => $product->getId(),
                'currency' => $channel->getCurrency()
            ])
            ->willReturn($assembledChannelPriceList);

        $this->registry
            ->expects(static::exactly(3))
            ->method('getManagerForClass')
            ->willReturnSelf();
        $this->registry
            ->expects(static::exactly(3))
            ->method('getRepository')
            ->withConsecutive(
                [Product::class],
                [AssembledPriceList::class],
                [AssembledChannelPriceList::class]
            )
            ->willReturnOnConsecutiveCalls(
                $productRepository,
                $productPriceRepository,
                $productChannelPriceRepository
            );

        $expectedData = [
            'price' => [
                $this->channelPriceProvider->getRowIdentifier($product->getId()-1, $product->getId()) => [
                    'value' => $expectedValue,
                ]
            ]
        ];

        $this->context = new FormChangeContext([
            FormChangeContext::FORM_FIELD => $form,
            FormChangeContext::SUBMITTED_DATA_FIELD => [
                OrderItemFormChangesProvider::ITEMS_FIELD => [['product' => $product->getId()]]
            ],
            FormChangeContext::RESULT_FIELD => []
        ]);

        $this->channelPriceProvider->processFormChanges($this->context);

        static::assertEquals(
            [OrderItemFormChangesProvider::ITEMS_FIELD => $expectedData],
            $this->context->getResult()
        );
    }

    public function processFormChangesDataProvider()
    {
        $defaultPrice = $this->getEntity(ProductPrice::class, ['id' => 1, 'value' => 100, 'currency' => 'EUR']);
        $specialPrice = $this->getEntity(ProductPrice::class, ['id' => 2, 'value' => 50, 'currency' => 'EUR']);

        $defaultChannelPrice = $this->getEntity(
            ProductChannelPrice::class,
            [
                'id' => 1,
                'value' => 40,
                'currency' => 'EUR'
            ]
        );

        $specialChannelPrice = $this->getEntity(
            ProductChannelPrice::class,
            [
                'id' => 2,
                'value' => 30,
                'currency' => 'EUR'
            ]
        );

        return [
            'noChannelPriceNoSpecialPrice' => [
                'assembledChannelPriceList' => null,
                'assembledPriceList' => $this->getEntity(
                    AssembledPriceList::class,
                    ['id' => 1, 'defaultPrice' => $defaultPrice]
                ),
                'expectedValue' => 100
            ],
            'noChannelPriceWithSpecialPrice' => [
                'assembledChannelPriceList' => null,
                'assembledPriceList' => $this->getEntity(
                    AssembledPriceList::class,
                    ['id' => 1, 'defaultPrice' => $defaultPrice, 'specialPrice' => $specialPrice]
                ),
                'expectedValue' => 50
            ],
            'withChannelPriceNoSpecialPrice' => [
                'channelPrices' => $this->getEntity(
                    AssembledChannelPriceList::class,
                    ['id' => 1, 'defaultPrice' => $defaultChannelPrice]
                ),
                'defaultPrice' => $this->getEntity(
                    AssembledPriceList::class,
                    ['id' => 1, 'defaultPrice' => $defaultPrice, 'specialPrice' => $specialPrice]
                ),
                'expectedValue' => 40
            ],
            'withChannelPriceWithSpecialPrice' => [
                'channelPrices' => $this->getEntity(
                    AssembledChannelPriceList::class,
                    ['id' => 1, 'defaultPrice' => $defaultChannelPrice, 'specialPrice' => $specialChannelPrice]
                ),
                'defaultPrice' => $this->getEntity(
                    AssembledPriceList::class,
                    ['id' => 1, 'defaultPrice' => $defaultPrice, 'specialPrice' => $specialPrice]
                ),
                'expectedValue' => 30
            ]
        ];
    }

    /**
     * @dataProvider getChannelPriceDataProvider
     *
     * @param AssembledChannelPriceList $assembledChannelPriceList
     * @param array $expectedData
     */
    public function testGetChannelPrice(AssembledChannelPriceList $assembledChannelPriceList = null, $expectedData)
    {
        /** @var SalesChannel $channel */
        $channel = $this->getEntity(SalesChannel::class, ['id' => 1, 'currency' => 'EUR']);
        /** @var Product $product */
        $product = $this->getEntity(Product::class, ['id' => 1]);
        $repository = $this->createMock(EntityRepository::class);

        $this->registry
            ->expects(static::once())
            ->method('getManagerForClass')
            ->with(AssembledChannelPriceList::class)
            ->willReturnSelf();
        $this->registry
            ->expects(static::once())
            ->method('getRepository')
            ->with(AssembledChannelPriceList::class)
            ->willReturn($repository);
        $repository
            ->expects(static::once())
            ->method('findOneBy')
            ->with([
                'channel' => $channel->getId(),
                'product' => $product->getId(),
                'currency' => $channel->getCurrency()
            ])
            ->willReturn($assembledChannelPriceList);

        static::assertEquals($expectedData, $this->channelPriceProvider->getChannelPrice($channel, $product));
    }

    public function getChannelPriceDataProvider()
    {
        $defaultChannelPrice = $this->getEntity(
            ProductChannelPrice::class,
            [
                'id' => 1,
                'value' => 40,
                'currency' => 'EUR'
            ]
        );
        $specialChannelPrice = $this->getEntity(
            ProductChannelPrice::class,
            [
                'id' => 2,
                'value' => 30,
                'currency' => 'EUR'
            ]
        );


        return [
            'noPrice' => [
                'assembledChannelPriceList' => null,
                'expectedData' => ['hasPrice' => false]
            ],
            'withoutSpecialPrice' => [
                'assembledChannelPriceList' =>  $this->getEntity(
                    AssembledChannelPriceList::class,
                    ['id' => 1, 'defaultPrice' => $defaultChannelPrice]
                ),
                'expectedData' => ['hasPrice' => true, 'price' => 40]
            ],
            'withSpecialPrice' => [
                'assembledChannelPriceList' =>  $this->getEntity(
                    AssembledChannelPriceList::class,
                    ['id' => 1, 'defaultPrice' => $defaultChannelPrice, 'specialPrice' => $specialChannelPrice]
                ),
                'expectedData' => ['hasPrice' => true, 'price' => 30]
            ]
        ];
    }

    /**
     * @dataProvider getDefaultPriceDataProvider
     *
     * @param AssembledPriceList $assembledPriceList
     * @param int|null $expectedValue
     */
    public function testGetDefaultPrice(AssembledPriceList $assembledPriceList = null, $expectedValue)
    {
        /** @var SalesChannel $channel */
        $channel = $this->getEntity(SalesChannel::class, ['id' => 1, 'currency' => 'EUR']);

        /** @var Product $product */
        $product = $this->getEntity(Product::class, ['id' => 1]);
        $productPriceRepository = $this->createMock(EntityRepository::class);
        $productPriceRepository
            ->expects(static::once())
            ->method('findOneBy')
            ->with(['product' => $product->getId(), 'currency' => $channel->getCurrency()])
            ->willReturn($assembledPriceList);

        $this->registry
            ->expects(static::once())
            ->method('getManagerForClass')
            ->with(AssembledPriceList::class)
            ->willReturnSelf();
        $this->registry
            ->expects(static::once())
            ->method('getRepository')
            ->with(AssembledPriceList::class)
            ->willReturn($productPriceRepository);

        static::assertEquals($expectedValue, $this->channelPriceProvider->getDefaultPrice($channel, $product));
    }

    public function getDefaultPriceDataProvider()
    {
        $defaultPrice = $this->getEntity(ProductPrice::class, ['id' => 1, 'value' => 100, 'currency' => 'EUR']);
        $specialPrice = $this->getEntity(ProductPrice::class, ['id' => 2, 'value' => 50, 'currency' => 'EUR']);

        return [
            'noPrice' => [
                'assembledChannelPriceList' => null,
                'expectedValue' => null
            ],
            'withoutSpecialPrice' => [
                'assembledChannelPriceList' =>  $this->getEntity(
                    AssembledPriceList::class,
                    ['id' => 1, 'defaultPrice' => $defaultPrice]
                ),
                'expectedValue' => 100
            ],
            'withSpecialPrice' => [
                'assembledChannelPriceList' =>  $this->getEntity(
                    AssembledPriceList::class,
                    ['id' => 1, 'defaultPrice' => $defaultPrice, 'specialPrice' => $specialPrice]
                ),
                'expectedValue' => 50
            ]
        ];
    }
}
