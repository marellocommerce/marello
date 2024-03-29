<?php

namespace Marello\Bundle\ShippingBundle\Tests\Unit\Provider;

use Marello\Bundle\ShippingBundle\Method\ShippingMethodProviderInterface;
use Marello\Bundle\ShippingBundle\Provider\BasicShippingMethodChoicesProvider;
use Marello\Bundle\ShippingBundle\Tests\Unit\Provider\Stub\ShippingMethodStub;
use Oro\Component\Testing\Unit\EntityTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

class BasicShippingMethodChoicesProviderTest extends \PHPUnit\Framework\TestCase
{
    use EntityTrait;

    /**
     * @var ShippingMethodProviderInterface|\PHPUnit\Framework\MockObject\MockObject phpdoc
     */
    protected $shippingMethodProvider;

    /**
     * @var TranslatorInterface|\PHPUnit\Framework\MockObject\MockObject phpdoc
     */
    protected $translator;

    /**
     * @var BasicShippingMethodChoicesProvider
     */
    protected $choicesProvider;

    protected function setUp(): void
    {
        $this->shippingMethodProvider = $this->createMock(ShippingMethodProviderInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->choicesProvider = new BasicShippingMethodChoicesProvider(
            $this->shippingMethodProvider,
            $this->translator
        );
    }

    /**
     * @param array $methods
     * @param array $result
     * @param bool  $translate
     *
     * @dataProvider methodsProvider
     */
    public function testGetMethods($methods, $result, $translate = false)
    {
        $translation = [
            ['flat rate', [], null, null, 'flat rate translated'],
            ['ups', [], null, null, 'ups translated'],
        ];

        $this->shippingMethodProvider->expects($this->once())
            ->method('getShippingMethods')
            ->willReturn($methods);

        $this->translator->expects($this->any())
            ->method('trans')
            ->will($this->returnValueMap($translation));

        $this->assertEquals($result, $this->choicesProvider->getMethods($translate));
    }

    /**
     * @return array
     */
    public function methodsProvider()
    {
        return
            [
                'some_methods' =>
                    [
                        'methods' =>
                            [
                                'ups' => $this->getEntity(
                                    ShippingMethodStub::class,
                                    [
                                        'identifier' => 'ups',
                                        'sortOrder' => 1,
                                        'label' => 'ups',
                                        'isEnabled' => false,
                                        'types' => [],
                                    ]
                                ),
                                'flat_rate' => $this->getEntity(
                                    ShippingMethodStub::class,
                                    [
                                        'identifier' => 'flat_rate',
                                        'sortOrder' => 1,
                                        'label' => 'flat rate',
                                        'isEnabled' => true,
                                        'types' => [],
                                    ]
                                ),
                            ],
                        'result' => ['ups' => 'ups', 'flat_rate' => 'flat rate'],
                        'translate' => false,
                    ],
                'some_methods_with_translation' =>
                    [
                        'methods' =>
                            [
                                'flat_rate' => $this->getEntity(
                                    ShippingMethodStub::class,
                                    [
                                        'identifier' => 'flat_rate',
                                        'sortOrder' => 1,
                                        'label' => 'flat rate',
                                        'isEnabled' => true,
                                        'types' => [],
                                    ]
                                ),
                                'ups' => $this->getEntity(
                                    ShippingMethodStub::class,
                                    [
                                        'identifier' => 'ups',
                                        'sortOrder' => 1,
                                        'label' => 'ups',
                                        'isEnabled' => false,
                                        'types' => [],
                                    ]
                                ),
                            ],
                        'result' => ['flat_rate' => 'flat rate translated', 'ups' => 'ups translated'],
                        'translate' => true,
                    ],
                'no_methods' =>
                    [
                        'methods' => [],
                        'result' => [],
                    ],
            ];
    }
}
