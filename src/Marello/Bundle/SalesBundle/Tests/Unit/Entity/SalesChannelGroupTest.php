<?php

namespace Marello\Bundle\SalesBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;

use Oro\Component\Testing\Unit\EntityTestCaseTrait;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

use Marello\Bundle\SalesBundle\Entity\SalesChannelGroup;

class SalesChannelGroupTest extends TestCase
{
    use EntityTestCaseTrait;

    public function testAccessors()
    {
        $this->assertPropertyAccessors(new SalesChannelGroup(), [
            ['name', 'some string', false],
            ['description', 'some string', false],
            ['system', true, true],
            ['organization', new Organization()]
        ]);
    }
}
