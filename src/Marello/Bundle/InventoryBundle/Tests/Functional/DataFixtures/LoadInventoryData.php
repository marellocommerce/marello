<?php

namespace Marello\Bundle\InventoryBundle\Tests\Functional\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Marello\Bundle\InventoryBundle\Entity\InventoryLevel;
use Marello\Bundle\InventoryBundle\Model\InventoryUpdateContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\InventoryBundle\Entity\Warehouse;
use Marello\Bundle\SalesBundle\Entity\SalesChannelGroup;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\ProductBundle\Entity\ProductInterface;
use Marello\Bundle\InventoryBundle\Manager\InventoryManager;
use Marello\Bundle\InventoryBundle\Entity\BalancedInventoryLevel;
use Marello\Bundle\InventoryBundle\Model\InventoryUpdateContextFactory;
use Marello\Bundle\InventoryBundle\Entity\Repository\WarehouseRepository;
use Marello\Bundle\ProductBundle\Tests\Functional\DataFixtures\LoadProductData;
use Marello\Bundle\InventoryBundle\Model\BalancedInventory\BalancedInventoryHandler;

class LoadInventoryData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
{
    /**
     * @var Organization
     */
    protected $defaultOrganization;

    /**
     * @var Warehouse
     */
    protected $defaultWarehouse;

    /**
     * @var array
     */
    protected $replenishments;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            LoadProductData::class,
            LoadWarehouseChannelLinkData::class
        ];
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $organizations = $this->manager
            ->getRepository(Organization::class)
            ->findAll();

        if (is_array($organizations) && count($organizations) > 0) {
            $this->defaultOrganization = array_shift($organizations);
        }

        /** @var AclHelper $aclHelper */
        $aclHelper = $this->container->get('oro_security.acl_helper');
        $this->defaultWarehouse = $this->container
            ->get(WarehouseRepository::class)
            ->getDefault($aclHelper);

        $this->loadProductInventory();
    }

    /**
     * load products
     */
    public function loadProductInventory()
    {
        $handle = fopen($this->getDictionary('product_inventory.csv'), "r");
        if ($handle) {
            $headers = [];
            if (($data = fgetcsv($handle, 1000, ",")) !== false) {
                //read headers
                $headers = $data;
            }
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $data = array_combine($headers, array_values($data));

                $this->createProductInventory($data);
            }
            fclose($handle);
        }
        $this->manager->flush();
    }

    /**
     * create new products and inventory items
     * @param array $data
     */
    private function createProductInventory(array $data)
    {
        $product = $this->manager
            ->getRepository(Product::class)
            ->findOneBy(['sku' => $data['sku']]);

        if ($product) {
            $inventoryItemManager = $this->container->get('marello_inventory.manager.inventory_item_manager');
            /** @var InventoryItem $inventoryItem */
            $inventoryItem = $inventoryItemManager->getInventoryItem($product);

            if (!$inventoryItem) {
                return;
            }
            if ($data['orderOnDemandAllowed'] === 'true') {
                $inventoryItem->setOrderOnDemandAllowed(true);
                $this->manager->persist($inventoryItem);
            }

            $replenishmentClass = ExtendHelper::buildEnumValueClassName('marello_inv_reple');
            $replenishment = $this->manager->getRepository($replenishmentClass)->find($data['replenishment']);
            $inventoryItem->setReplenishment($replenishment);
            $inventoryItem->setPurchaseInventory($data['purchaseInventory']);
            $inventoryItem->setDesiredInventory($data['desiredInventory']);

            $this->handleInventoryUpdate($product, $inventoryItem, $data['inventory_qty'], 0, null);
            $this->balanceInventory($product, $data['inventory_qty']);
            $this->setReference(
                sprintf('marello_inventoryitem_%s',
                    $inventoryItem->getProduct()->getSku()
                ),
                $inventoryItem
            );
            foreach ($inventoryItem->getInventoryLevels() as $k => $level) {
                $this->setReference(
                    sprintf('marello_inventorylvl_%s_%s',
                        $k,
                        $inventoryItem->getProduct()->getSku()
                    ),
                    $level
                );
            }
        }
    }

    /**
     * handle the inventory update for items which have been shipped
     * @param ProductInterface $product
     * @param InventoryItem $item
     * @param $inventoryUpdateQty
     * @param $allocatedInventoryQty
     * @param $entity
     */
    protected function handleInventoryUpdate($product, $item, $inventoryUpdateQty, $allocatedInventoryQty, $entity)
    {
        $context = InventoryUpdateContextFactory::createInventoryUpdateContext(
            $product,
            $item,
            $inventoryUpdateQty,
            $allocatedInventoryQty,
            'import',
            $entity
        );

        /** @var InventoryManager $inventoryManager */
        $inventoryManager = $this->container->get('marello_inventory.manager.inventory_manager');
        $context->setValue('isInventoryManaged', true);
        $inventoryManager->updateInventoryLevel($context);
    }

    /**
     * @param Product $product
     * @param $inventoryQty $int
     */
    public function balanceInventory($product, $inventoryQty)
    {
        /** @var SalesChannel[] $salesChannels */
        $salesChannels = $product->getChannels();
        $salesChannelGroups = [];
        foreach ($salesChannels as $salesChannel) {
            /** @var SalesChannelGroup[] $salesChannelGroups */
            $salesChannelGroups[$salesChannel->getGroup()->getId()] = $salesChannel->getGroup();
        }
        /** @var BalancedInventoryHandler $handler */
        $handler = $this->container->get('marello_inventory.model.balancedinventory.balanced_inventory_handler');
        $balancedQty = ($inventoryQty / count($salesChannelGroups));
        foreach ($salesChannelGroups as $salesChannelGroup) {
            /** @var BalancedInventoryLevel $level */
            $level = $handler->findExistingBalancedInventory($product, $salesChannelGroup);
            if (!$level) {
                $level = $handler->createBalancedInventoryLevel($product, $salesChannelGroup, $balancedQty);
            }
            $handler->saveBalancedInventory($level, true, true);
            $this->setReference(
                sprintf('marello_balancedinvlev_%s_%s', $product->getSku(), $salesChannelGroup->getName()),
                $level
            );
        }
    }

    /**
     * Get dictionary file by name
     * @param $name
     * @return string
     */
    protected function getDictionary($name)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'dictionaries' . DIRECTORY_SEPARATOR . $name;
    }
}
