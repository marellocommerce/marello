<?php

namespace Marello\Bundle\ShippingBundle\Tests\Unit\Provider;

use Oro\Bundle\CurrencyBundle\Entity\Price;
use Marello\Bundle\ShippingBundle\Context\LineItem\Collection\Doctrine\DoctrineShippingLineItemCollection;
use Marello\Bundle\ShippingBundle\Context\ShippingContext;
use Marello\Bundle\ShippingBundle\Context\ShippingLineItem;
use Marello\Bundle\ShippingBundle\Entity\ShippingMethodConfig;
use Marello\Bundle\ShippingBundle\Entity\ShippingMethodsConfigsRule;
use Marello\Bundle\ShippingBundle\Entity\ShippingMethodTypeConfig;
use Marello\Bundle\ShippingBundle\Event\ApplicableMethodsEvent;
use Marello\Bundle\ShippingBundle\Method\ShippingMethodProviderInterface;
use Marello\Bundle\ShippingBundle\Method\ShippingMethodViewFactory;
use Marello\Bundle\ShippingBundle\Provider\Cache\ShippingPriceCache;
use Marello\Bundle\ShippingBundle\Provider\MethodsConfigsRule\Context\MethodsConfigsRulesByContextProviderInterface;
use Marello\Bundle\ShippingBundle\Provider\Price\ShippingPriceProvider;
use Marello\Bundle\ShippingBundle\Tests\Unit\Provider\Stub\PriceAwareShippingMethodStub;
use Marello\Bundle\ShippingBundle\Tests\Unit\Provider\Stub\ShippingMethodStub;
use Marello\Bundle\ShippingBundle\Tests\Unit\Provider\Stub\ShippingMethodTypeStub;
use Oro\Component\Testing\Unit\EntityTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ShippingPriceProviderTest extends \PHPUnit\Framework\TestCase
{
    use EntityTrait;

    /**
     * @var MethodsConfigsRulesByContextProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $shippingRulesProvider;

    /**
     * @var ShippingMethodProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $shippingMethodProvider;

    /**
     * @var ShippingPriceCache|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $priceCache;

    /**
     * @var EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventDispatcher;

    /**
     * @var ShippingPriceProvider
     */
    protected $shippingPriceProvider;

    protected function setUp(): void
    {
        $this->shippingRulesProvider = $this->getMockBuilder(MethodsConfigsRulesByContextProviderInterface::class)
            ->disableOriginalConstructor()->getMock();

        $methods = [
            'flat_rate' => $this->getEntity(ShippingMethodStub::class, [
                'identifier' => 'flat_rate',
                'sortOrder' => 1,
                'types' => [
                    'primary' => $this->getEntity(ShippingMethodTypeStub::class, [
                        'identifier' => 'primary',
                        'sortOrder' => 1,
                    ])
                ]
            ]),
            'integration_method' => $this->getEntity(PriceAwareShippingMethodStub::class, [
                'identifier' => 'integration_method',
                'sortOrder' => 2,
                'isGrouped' => true,
                'types' => [
                    'ground' => $this->getEntity(ShippingMethodTypeStub::class, [
                        'identifier' => 'ground',
                        'sortOrder' => 1,
                    ]),
                    'air' => $this->getEntity(ShippingMethodTypeStub::class, [
                        'identifier' => 'air',
                        'sortOrder' => 2,
                    ])
                ]
            ])
        ];

        $this->shippingMethodProvider = $this->createMock(ShippingMethodProviderInterface::class);
        $this->shippingMethodProvider->expects($this->any())
            ->method('getShippingMethod')
            ->will($this->returnCallback(function ($methodId) use ($methods) {
                return array_key_exists($methodId, $methods) ? $methods[$methodId] : null;
            }));

        $this->priceCache = $this->getMockBuilder(ShippingPriceCache::class)
            ->disableOriginalConstructor()->getMock();

        $viewFactory = new ShippingMethodViewFactory($this->shippingMethodProvider);

        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->shippingPriceProvider = new ShippingPriceProvider(
            $this->shippingRulesProvider,
            $this->shippingMethodProvider,
            $this->priceCache,
            $viewFactory,
            $this->eventDispatcher
        );
    }

    /**
     * @dataProvider getApplicableShippingMethodsConfigsRulesProvider
     *
     * @param array $shippingRules
     * @param array $expectedData
     */
    public function testGetApplicableMethodsViews(array $shippingRules, array $expectedData)
    {
        $shippingLineItems = [new ShippingLineItem([])];

        $sourceEntity = new \stdClass();

        $context = new ShippingContext([
            ShippingContext::FIELD_LINE_ITEMS => new DoctrineShippingLineItemCollection($shippingLineItems),
            ShippingContext::FIELD_CURRENCY => 'USD',
            ShippingContext::FIELD_SOURCE_ENTITY => $sourceEntity
        ]);

        $this->shippingRulesProvider->expects($this->once())
            ->method('getShippingMethodsConfigsRules')
            ->with($context)
            ->willReturn($shippingRules);

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(ApplicableMethodsEvent::class), ApplicableMethodsEvent::NAME);

        $this->assertEquals(
            $expectedData,
            $this->shippingPriceProvider
                ->getApplicableMethodsViews($context)
                ->toArray()
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return array
     */
    public function getApplicableShippingMethodsConfigsRulesProvider()
    {
        return [
            'one rule' => [
                'shippingRule' => [
                    $this->getEntity(ShippingMethodsConfigsRule::class, [
                        'methodConfigs' => [
                            $this->getEntity(ShippingMethodConfig::class, [
                                'method' => 'flat_rate',
                                'typeConfigs' => [
                                    $this->getEntity(ShippingMethodTypeConfig::class, [
                                        'enabled' => true,
                                        'type' => 'primary',
                                        'options' => [
                                            'price' => Price::create(12, 'USD'),
                                        ],
                                    ])
                                ],
                            ])
                        ]
                    ]),
                    $this->getEntity(ShippingMethodsConfigsRule::class, [
                        'methodConfigs' => [
                            $this->getEntity(ShippingMethodConfig::class, [
                                'method' => 'integration_method',
                                'typeConfigs' => [
                                    $this->getEntity(ShippingMethodTypeConfig::class, [
                                        'enabled' => true,
                                        'type' => 'ground',
                                        'options' => [
                                            'aware_price' => null,
                                        ],
                                    ])
                                ],
                            ])
                        ]
                    ]),
                    $this->getEntity(ShippingMethodsConfigsRule::class, [
                        'methodConfigs' => [
                            $this->getEntity(ShippingMethodConfig::class, [
                                'method' => 'unknown_method',
                            ])
                        ]
                    ])
                ],
                'expectedData' => [
                    'flat_rate' => [
                        'identifier' => 'flat_rate',
                        'label' => 'flat_rate.label',
                        'sortOrder' => 1,
                        'isGrouped' => false,
                        'types' => [
                            'primary' => [
                                'identifier' => 'primary',
                                'label' => 'primary.label',
                                'sortOrder' => 1,
                                'price' => Price::create(12, 'USD')
                            ]
                        ]
                    ]
                ]
            ],
            'several rules with same methods ans diff types' => [
                'shippingRule' => [
                    $this->getEntity(ShippingMethodsConfigsRule::class, [
                        'methodConfigs' => [
                            $this->getEntity(ShippingMethodConfig::class, [
                                'method' => 'integration_method',
                                'typeConfigs' => [
                                    $this->getEntity(ShippingMethodTypeConfig::class, [
                                        'enabled' => true,
                                        'type' => 'ground',
                                        'options' => [
                                            'aware_price' => Price::create(2, 'USD'),
                                        ],
                                    ]),
                                ],
                            ]),
                        ]
                    ]),
                    $this->getEntity(ShippingMethodsConfigsRule::class, [
                        'methodConfigs' => [
                            $this->getEntity(ShippingMethodConfig::class, [
                                'method' => 'flat_rate',
                                'typeConfigs' => [
                                    $this->getEntity(ShippingMethodTypeConfig::class, [
                                        'enabled' => true,
                                        'type' => 'primary',
                                        'options' => [
                                            'price' => Price::create(1, 'USD'),
                                        ],
                                    ])
                                ],
                            ])
                        ]
                    ]),
                    $this->getEntity(ShippingMethodsConfigsRule::class, [
                        'methodConfigs' => [
                            $this->getEntity(ShippingMethodConfig::class, [
                                'method' => 'integration_method',
                                'typeConfigs' => [
                                    $this->getEntity(ShippingMethodTypeConfig::class, [
                                        'enabled' => true,
                                        'type' => 'ground',
                                        'options' => [
                                            'aware_price' => Price::create(3, 'USD'),
                                        ],
                                    ]),
                                    $this->getEntity(ShippingMethodTypeConfig::class, [
                                        'enabled' => true,
                                        'type' => 'air',
                                        'options' => [
                                            'aware_price' => Price::create(4, 'USD'),
                                        ],
                                    ]),
                                ],
                            ]),
                            $this->getEntity(ShippingMethodConfig::class, [
                                'method' => 'flat_rate',
                                'typeConfigs' => [
                                    $this->getEntity(ShippingMethodTypeConfig::class, [
                                        'enabled' => true,
                                        'type' => 'primary',
                                        'options' => [
                                            'price' => Price::create(5, 'USD'),
                                        ],
                                    ])
                                ],
                            ])
                        ]
                    ]),
                ],
                'expectedData' => [
                    'flat_rate' => [
                        'identifier' => 'flat_rate',
                        'label' => 'flat_rate.label',
                        'sortOrder' => 1,
                        'isGrouped' => false,
                        'types' => [
                            'primary' => [
                                'identifier' => 'primary',
                                'label' => 'primary.label',
                                'sortOrder' => 1,
                                'price' => Price::create(1, 'USD'),
                            ]
                        ]
                    ],
                    'integration_method' => [
                        'identifier' => 'integration_method',
                        'label' => 'integration_method.label',
                        'sortOrder' => 2,
                        'isGrouped' => true,
                        'types' => [
                            'ground' => [
                                'identifier' => 'ground',
                                'label' => 'ground.label',
                                'sortOrder' => 1,
                                'price' => Price::create(2, 'USD'),
                            ],
                            'air' => [
                                'identifier' => 'air',
                                'label' => 'air.label',
                                'sortOrder' => 2,
                                'price' => Price::create(4, 'USD'),
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

    public function testGetApplicableMethodsViewsCache()
    {
        $shippingLineItems = [new ShippingLineItem([])];

        $context = new ShippingContext([
            ShippingContext::FIELD_LINE_ITEMS => new DoctrineShippingLineItemCollection($shippingLineItems),
            ShippingContext::FIELD_CURRENCY => 'USD'
        ]);

        $this->shippingRulesProvider->expects(static::exactly(2))
            ->method('getShippingMethodsConfigsRules')
            ->with($context)
            ->willReturn([
                $this->getEntity(ShippingMethodsConfigsRule::class, [
                    'methodConfigs' => [
                        $this->getEntity(ShippingMethodConfig::class, [
                            'method' => 'flat_rate',
                            'typeConfigs' => [
                                $this->getEntity(ShippingMethodTypeConfig::class, [
                                    'enabled' => true,
                                    'type' => 'primary',
                                    'options' => [
                                        'price' => Price::create(1, 'USD'),
                                    ],
                                ])
                            ],
                        ])
                    ]
                ])
            ]);
        $price = Price::create(1, 'USD');

        $expectedData = [
            'flat_rate' => [
                'identifier' => 'flat_rate',
                'label' => 'flat_rate.label',
                'sortOrder' => 1,
                'isGrouped' => false,
                'types' => [
                    'primary' => [
                        'identifier' => 'primary',
                        'label' => 'primary.label',
                        'sortOrder' => 1,
                        'price' => $price
                    ]
                ]
            ]
        ];

        $this->priceCache->expects(static::exactly(2))
            ->method('hasPrice')
            ->withConsecutive(
                [$context, 'flat_rate', 'primary'],
                [$context, 'flat_rate', 'primary']
            )
            ->willReturnOnConsecutiveCalls(false, true);

        $this->priceCache->expects(static::once())
            ->method('savePrice')
            ->with($context, 'flat_rate', 'primary', Price::create(1, 'USD'))
            ->willReturn(true);

        $this->priceCache->expects(static::once())
            ->method('getPrice')
            ->with($context, 'flat_rate', 'primary')
            ->willReturn(Price::create(2, 'USD'));

        $this->assertEquals(
            $expectedData,
            $this->shippingPriceProvider->getApplicableMethodsViews($context)->toArray()
        );
        $price->setValue(2);
        $this->assertEquals(
            $expectedData,
            $this->shippingPriceProvider->getApplicableMethodsViews($context)->toArray()
        );
    }

    /**
     * @dataProvider getPriceDataProvider
     *
     * @param string $methodId
     * @param string $typeId
     * @param array $shippingRules
     * @param Price|null $expectedPrice
     */
    public function testGetPrice($methodId, $typeId, array $shippingRules, Price $expectedPrice = null)
    {
        $shippingLineItems = [new ShippingLineItem([])];

        $context = new ShippingContext([
            ShippingContext::FIELD_LINE_ITEMS => new DoctrineShippingLineItemCollection($shippingLineItems),
            ShippingContext::FIELD_CURRENCY => 'USD'
        ]);

        $this->shippingRulesProvider->expects($this->once())
            ->method('getShippingMethodsConfigsRules')
            ->with($context)
            ->willReturn($shippingRules);

        $this->priceCache->expects($this->exactly($expectedPrice ? 1 : 0))->method('savePrice');

        $this->assertEquals($expectedPrice, $this->shippingPriceProvider->getPrice($context, $methodId, $typeId));
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return array
     */
    public function getPriceDataProvider()
    {
        return [
            'no rule' => [
                'methodId' => 'integration_method',
                'typeId' => 'ground',
                'shippingRules' => [
                    $this->getEntity(ShippingMethodsConfigsRule::class, [
                        'methodConfigs' => [
                            $this->getEntity(ShippingMethodConfig::class, [
                                'method' => 'flat_rate',
                                'typeConfigs' => [
                                    $this->getEntity(ShippingMethodTypeConfig::class, [
                                        'enabled' => true,
                                        'type' => 'primary',
                                        'options' => [
                                            'price' => Price::create(12, 'USD'),
                                        ],
                                    ])
                                ],
                            ])
                        ]
                    ]),
                ],
                'expectedData' => null,
            ],
            'one rule' => [
                'methodId' => 'flat_rate',
                'typeId' => 'primary',
                'shippingRules' => [
                    $this->getEntity(ShippingMethodsConfigsRule::class, [
                        'methodConfigs' => [
                            $this->getEntity(ShippingMethodConfig::class, [
                                'method' => 'flat_rate',
                                'typeConfigs' => [
                                    $this->getEntity(ShippingMethodTypeConfig::class, [
                                        'enabled' => true,
                                        'type' => 'primary',
                                        'options' => [
                                            'price' => Price::create(12, 'USD'),
                                        ],
                                    ])
                                ],
                            ])
                        ]
                    ]),
                ],
                'expectedData' => Price::create(12, 'USD'),
            ],
            'no price' => [
                'methodId' => 'flat_rate',
                'typeId' => 'primary',
                'shippingRules' => [
                    $this->getEntity(ShippingMethodsConfigsRule::class, [
                        'methodConfigs' => [
                            $this->getEntity(ShippingMethodConfig::class, [
                                'method' => 'flat_rate',
                                'typeConfigs' => [
                                    $this->getEntity(ShippingMethodTypeConfig::class, [
                                        'enabled' => true,
                                        'type' => 'primary',
                                        'options' => [
                                            'price' => null
                                        ],
                                    ])
                                ],
                            ])
                        ]
                    ]),
                ],
                'expectedData' => null,
            ],
            'several rules with same methods ans types' => [
                'methodId' => 'integration_method',
                'typeId' => 'ground',
                'shippingRules' => [
                    $this->getEntity(ShippingMethodsConfigsRule::class, [
                        'methodConfigs' => [
                            $this->getEntity(ShippingMethodConfig::class, [
                                'method' => 'integration_method',
                                'typeConfigs' => [
                                    $this->getEntity(ShippingMethodTypeConfig::class, [
                                        'enabled' => true,
                                        'type' => 'ground',
                                        'options' => [
                                            'price' => Price::create(1, 'USD'),
                                        ],
                                    ])
                                ],
                            ]),
                        ]
                    ]),
                    $this->getEntity(ShippingMethodsConfigsRule::class, [
                        'methodConfigs' => [
                            $this->getEntity(ShippingMethodConfig::class, [
                                'method' => 'integration_method',
                                'typeConfigs' => [
                                    $this->getEntity(ShippingMethodTypeConfig::class, [
                                        'enabled' => true,
                                        'type' => 'ground',
                                        'options' => [
                                            'price' => Price::create(2, 'USD'),
                                        ],
                                    ])
                                ],
                            ]),
                            $this->getEntity(ShippingMethodConfig::class, [
                                'method' => 'integration_method',
                                'typeConfigs' => [
                                    $this->getEntity(ShippingMethodTypeConfig::class, [
                                        'enabled' => true,
                                        'type' => 'air',
                                        'options' => [
                                            'price' => Price::create(3, 'USD'),
                                        ],
                                    ])
                                ],
                            ])
                        ]
                    ])
                ],
                'expectedData' => Price::create(1, 'USD'),
            ],
        ];
    }

    public function testGetPriceCache()
    {
        $methodId = 'flat_rate';
        $typeId = 'primary';

        $shippingLineItems = [new ShippingLineItem([])];

        $context = new ShippingContext([
            ShippingContext::FIELD_LINE_ITEMS => new DoctrineShippingLineItemCollection($shippingLineItems),
            ShippingContext::FIELD_CURRENCY => 'USD'
        ]);

        $this->shippingRulesProvider->expects($this->exactly(2))
            ->method('getShippingMethodsConfigsRules')
            ->with($context)
            ->willReturn([
                $this->getEntity(ShippingMethodsConfigsRule::class, [
                    'methodConfigs' => [
                        $this->getEntity(ShippingMethodConfig::class, [
                            'method' => $methodId,
                            'typeConfigs' => [
                                $this->getEntity(ShippingMethodTypeConfig::class, [
                                    'enabled' => true,
                                    'type' => $typeId,
                                    'options' => [
                                        'price' => Price::create(1, 'USD'),
                                    ],
                                ])
                            ],
                        ])
                    ]
                ])
            ]);

        $expectedPrice = Price::create(1, 'USD');

        $this->priceCache->expects(static::exactly(2))
            ->method('hasPrice')
            ->withConsecutive(
                [$context, 'flat_rate', 'primary'],
                [$context, 'flat_rate', 'primary']
            )
            ->willReturnOnConsecutiveCalls(false, true);

        $this->priceCache->expects(static::once())
            ->method('savePrice')
            ->with($context, 'flat_rate', 'primary', Price::create(1, 'USD'))
            ->willReturn(true);

        $this->priceCache->expects(static::once())
            ->method('getPrice')
            ->with($context, 'flat_rate', 'primary')
            ->willReturn(Price::create(2, 'USD'));

        $this->assertEquals($expectedPrice, $this->shippingPriceProvider->getPrice($context, $methodId, $typeId));
        $expectedPrice->setValue(2);
        $this->assertEquals($expectedPrice, $this->shippingPriceProvider->getPrice($context, $methodId, $typeId));
    }

    public function testGetPriceNoMethodAndType()
    {
        $shippingLineItems = [new ShippingLineItem([])];

        $context = new ShippingContext([
            ShippingContext::FIELD_LINE_ITEMS => new DoctrineShippingLineItemCollection($shippingLineItems),
            ShippingContext::FIELD_CURRENCY => 'USD'
        ]);

        $this->assertNull($this->shippingPriceProvider->getPrice($context, 'unknown_method', 'primary'));
        $this->assertNull($this->shippingPriceProvider->getPrice($context, 'flat_rate', 'unknown_method'));
    }
}
