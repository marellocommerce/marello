<?php

namespace Marello\Bundle\TaxBundle\Tests\Unit\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use PHPUnit\Framework\TestCase;

use Marello\Bundle\TaxBundle\DependencyInjection\Compiler\TaxMapperPass;

class TaxMapperPassTest extends TestCase
{
    /**
     * @var TaxMapperPass
     */
    protected $compilerPass;

    /**
     * @var ContainerBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $containerBuilder;

    public function setUp(): void
    {
        $this->containerBuilder = $this->createMock(ContainerBuilder::class);

        $this->compilerPass = new TaxMapperPass();
    }

    public function testProcessRegistryDoesNotExist()
    {
        $this->containerBuilder
            ->expects($this->once())
            ->method('hasDefinition')
            ->with(TaxMapperPass::REGISTRY_SERVICE)
            ->willReturn(false);

        $this->containerBuilder
            ->expects($this->never())
            ->method('getDefinition');

        $this->containerBuilder
            ->expects($this->never())
            ->method('findTaggedServiceIds');

        $this->compilerPass->process($this->containerBuilder);
    }

    public function testProcessNoTaggedServicesFound()
    {
        $this->containerBuilder
            ->expects($this->once())
            ->method('hasDefinition')
            ->with(TaxMapperPass::REGISTRY_SERVICE)
            ->willReturn(true);

        $this->containerBuilder
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->willReturn([]);

        $this->containerBuilder
            ->expects($this->never())
            ->method('getDefinition');

        $this->compilerPass->process($this->containerBuilder);
    }

    public function testProcessWithTaggedServices()
    {
        $this->containerBuilder
            ->expects($this->once())
            ->method('hasDefinition')
            ->with(TaxMapperPass::REGISTRY_SERVICE)
            ->willReturn(true);

        $registryServiceDefinition = $this->createMock(Definition::class);

        $this->containerBuilder
            ->expects($this->once())
            ->method('getDefinition')
            ->with(TaxMapperPass::REGISTRY_SERVICE)
            ->willReturn($registryServiceDefinition);

        $taggedServices = [
            'service.name.1' => [[]],
            'service.name.2' => [[]],
            'service.name.3' => [[]],
            'service.name.4' => [[]],
        ];

        $this->containerBuilder
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->willReturn($taggedServices);

        $registryServiceDefinition
            ->expects($this->exactly(4))
            ->method('addMethodCall')
            ->withConsecutive(
                ['addMapper', [new Reference('service.name.1')]],
                ['addMapper', [new Reference('service.name.2')]],
                ['addMapper', [new Reference('service.name.3')]],
                ['addMapper', [new Reference('service.name.4')]]
            );

        $this->compilerPass->process($this->containerBuilder);
    }
}
