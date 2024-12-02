<?php

namespace Marello\Bundle\SalesBundle\Tests\Unit\Entity;

use Marello\Bundle\SalesBundle\Entity\SalesChannelType;
use PHPUnit\Framework\TestCase;

use Oro\Bundle\LocaleBundle\Entity\Localization;
use Oro\Component\Testing\Unit\EntityTestCaseTrait;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

use Marello\Bundle\SalesBundle\Entity\SalesChannel;

class SalesChannelTest extends TestCase
{
    use EntityTestCaseTrait;

    public function testAccessors()
    {
        $this->assertPropertyAccessors(new SalesChannel(), [
            ['id', 42],
            ['name', 'some string', false],
            ['code', 'some string', false],
            ['currency', 'some string', false],
            ['active', true],
            ['default', true],
            ['organization', new Organization()],
            ['channelType', new SalesChannelType(), false],
            ['createdAt', new \DateTime()],
            ['updatedAt', new \DateTime()],
            ['localization', new Localization()]
        ]);
    }
}
