<?php

namespace Marello\Bundle\RuleBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;

use Oro\Component\Testing\Unit\EntityTrait;
use Oro\Component\Testing\Unit\EntityTestCaseTrait;

use Marello\Bundle\RuleBundle\Entity\Rule;

class RuleTest extends TestCase
{
    use EntityTestCaseTrait;
    use EntityTrait;

    public function testProperties()
    {
        $now = new \DateTime();
        $properties = [
            ['name', 'Test Rule', false],
            ['enabled', true],
            ['sortOrder', 10, false],
            ['stopProcessing', true],
            ['system', true],
            ['createdAt', $now, false],
            ['updatedAt', $now, false],
        ];

        $rule = new Rule();
        static::assertPropertyAccessors($rule, $properties);
    }
}
