<?php

namespace Marello\Bundle\RuleBundle\Tests\Unit\Form\Type;

use Marello\Bundle\RuleBundle\Entity\Rule;
use Marello\Bundle\RuleBundle\Entity\RuleInterface;
use Marello\Bundle\RuleBundle\Form\Type\RuleType;
use Oro\Component\Testing\Unit\FormIntegrationTestCase;

class RuleTypeTest extends FormIntegrationTestCase
{
    /** @var RuleType */
    protected $formType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formType = new RuleType();
    }

    public function testGetBlockPrefix()
    {
        $this->assertEquals(RuleType::BLOCK_PREFIX, $this->formType->getBlockPrefix());
    }

    /**
     * @dataProvider submitDataProvider
     *
     * @param RuleInterface $rule
     */
    public function testSubmitValid(RuleInterface $rule)
    {
        $form = $this->factory->create(RuleType::class, $rule);

        $this->assertSame($rule, $form->getData());

        $form->submit([
            'name' => 'new rule',
            'sortOrder' => '1'
        ]);

        $newRule = (new Rule())
            ->setName('new rule')
            ->setSortOrder(1)
            ->setEnabled(false)
            ->setSystem(false)
            ->setCreatedAt($rule->getCreatedAt())
            ->setUpdatedAt($rule->getUpdatedAt())
        ;

        $this->assertTrue($form->isValid());
        $this->assertEquals($newRule, $form->getData());
    }

    /**
     * @return array
     */
    public function submitDataProvider()
    {
        return [
            [new Rule()],
            [
                (new Rule())
                    ->setName('old name')
                    ->setSortOrder(3)
            ],
        ];
    }
}
