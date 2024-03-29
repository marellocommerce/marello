<?php

namespace Marello\Bundle\TaxBundle\Tests\Unit\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use PHPUnit\Framework\TestCase;

use Marello\Bundle\TaxBundle\Event\ResolveTaxEvent;
use Marello\Bundle\TaxBundle\Event\ResolverEventConnector;
use Marello\Bundle\TaxBundle\DependencyInjection\Compiler\ResolverEventConnectorPass;

class ResolverEventConnectorPassTest extends TestCase
{
    /**
     * @var ResolverEventConnectorPass
     */
    protected $compilerPass;

    /**
     * @var ContainerBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $containerBuilder;

    public function setUp(): void
    {
        $this->containerBuilder = $this->createMock(ContainerBuilder::class);

        $this->compilerPass = new ResolverEventConnectorPass();
    }

    public function testNoTaggedServices()
    {
        $this->containerBuilder
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->willReturn([]);

        $this->containerBuilder
            ->expects($this->never())
            ->method('setDefinition');

        $this->compilerPass->process($this->containerBuilder);
    }

    /**
     * @dataProvider emptyTagsDataProvider
     * @param array $tags
     * @param array $exception
     */
    public function testEmptyTags(array $tags, array $exception = [])
    {
        if ($exception) {
            list($exception, $message) = $exception;
            $this->expectException($exception);
            $this->expectExceptionMessage($message);
        }

        $this->containerBuilder
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->willReturn(['oro_tax.resolver.total' => $tags]);

        $this->containerBuilder
            ->expects($this->never())
            ->method('setDefinition');

        $this->compilerPass->process($this->containerBuilder);
    }

    /**
     * @return array
     */
    public function emptyTagsDataProvider()
    {
        return [
            'empty tag' => [[]],
            'empty event' => [
                [['priority' => -255]],
                ['\InvalidArgumentException', 'Wrong tags configuration "[{"priority":-255}]"'],
            ],
        ];
    }

    public function testTag()
    {
        $id = 'oro_tax.resolver.total';
        $class = ResolverEventConnector::class;

        $this->containerBuilder
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->willReturn(
                [
                    $id => [
                        ['event' => ResolveTaxEvent::RESOLVE, 'priority' => -255],
                        ['event' => ResolveTaxEvent::RESOLVE, 'priority' => 255],
                    ],
                ]
            );

        $this->containerBuilder
            ->expects($this->once())
            ->method('setDefinition')
            ->with(
                sprintf('%s.%s', $id, ResolverEventConnectorPass::CONNECTOR_SERVICE_NAME_SUFFIX),
                $this->callback(
                    function (Definition $definition) use ($class, $id) {
                        $this->assertEquals($class, $definition->getClass());
                        $this->assertEquals([new Reference($id)], $definition->getArguments());
                        $this->assertTrue($definition->hasTag('kernel.event_listener'));
                        $this->assertEquals(
                            [
                                ['event' => ResolveTaxEvent::RESOLVE, 'method' => 'onResolve', 'priority' => -255],
                                ['event' => ResolveTaxEvent::RESOLVE, 'method' => 'onResolve', 'priority' => 255],
                            ],
                            $definition->getTag('kernel.event_listener')
                        );

                        $this->assertTrue(method_exists($class, 'onResolve'));

                        return true;
                    }
                )
            );

        $this->compilerPass->process($this->containerBuilder);
    }
}
