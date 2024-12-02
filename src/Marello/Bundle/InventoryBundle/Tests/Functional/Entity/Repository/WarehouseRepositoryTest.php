<?php

namespace Marello\Bundle\InventoryBundle\Tests\Functional\Entity\Repository;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

use Marello\Bundle\InventoryBundle\Entity\Warehouse;
use Marello\Bundle\InventoryBundle\Entity\Repository\WarehouseRepository;

class WarehouseRepositoryTest extends WebTestCase
{
    /** @var WarehouseRepository */
    protected $repository;

    public function setUp(): void
    {
        $this->initClient();

        $this->repository = $this->getContainer()->get('doctrine')->getRepository(Warehouse::class);
    }

    /**
     * Tests if default really returns default warehouse.
     */
    public function testGetDefault()
    {
        $aclHelper = $this->getContainer()->get('oro_security.acl_helper');
        $result = $this->repository->getDefault($aclHelper);

        $this->assertTrue($result->isDefault(), 'Result of getDefault should be default repository.');
    }
}
