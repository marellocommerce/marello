<?php

namespace Marello\Bundle\ShippingBundle\Tests\Unit\Helper;

use Oro\Bundle\DataGridBundle\Tools\DatagridRouteHelper;
use Marello\Bundle\ShippingBundle\Tools\FilteredDatagridRouteHelper;
use Symfony\Component\Routing\RouterInterface;

class FilteredDatagridRouteHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DatagridRouteHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $datagridRouteHelper;

    /**
     * @var string $gridRouteName
     */
    protected $gridRouteName;

    /**
     * @var string $gridName
     */
    protected $gridName;

    /**
     * @var FilteredDatagridRouteHelper
     */
    protected $helper;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->datagridRouteHelper = $this->createMock(DatagridRouteHelper::class);

        $this->gridRouteName = 'route_name';
        $this->gridName = 'grid_name';

        $this->helper = new FilteredDatagridRouteHelper(
            $this->gridRouteName,
            $this->gridName,
            $this->datagridRouteHelper
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        unset($this->datagridRouteHelper, $this->helper);
    }

    public function testGenerate()
    {
        $this->datagridRouteHelper->expects($this->once())->method('generate')->with(
            $this->gridRouteName,
            $this->gridName,
            ['f' => ['filterName' => ['value' => ['' => '10']]]],
            RouterInterface::ABSOLUTE_PATH
        )->willReturn('generatedURL');

        $this->assertEquals('generatedURL', $this->helper->generate(['filterName' => 10]));
    }
}
