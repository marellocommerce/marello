<?php

namespace Marello\Bundle\ProductBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\Entity\Repository\ProductRepository;
use Marello\Bundle\ProductBundle\Async\Topic\ProductFilesUpdateTopic;

class ProductFilesUrlUpdateCommand extends Command
{
    const COMMAND_NAME = 'marello:product:url-update';
    const EXIT_CODE = 0;

    /** @var DoctrineHelper $doctrineHelper */
    protected $doctrineHelper;

    /** @var MessageProducerInterface $messageProducer */
    protected $messageProducer;

    /**
     * @param DoctrineHelper $doctrineHelper
     * @param MessageProducerInterface $messageProducer
     */
    public function __construct(
        DoctrineHelper $doctrineHelper,
        MessageProducerInterface $messageProducer
    ) {
        parent::__construct();
        
        $this->doctrineHelper = $doctrineHelper;
        $this->messageProducer = $messageProducer;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Update all image urls for products');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->getProductsToProcess() as $product) {
            // if the setting is changed, we need to update all the images
            // of products to regenerate the media urls
            $this->sendToMessageProducer($product['id']);
        }

        return self::EXIT_CODE;
    }

    protected function sendToMessageProducer(int $productId): void
    {
        $this->messageProducer->send(
            ProductFilesUpdateTopic::getName(),
            ['productId' => $productId]
        );
    }

    protected function getProductsToProcess(): iterable
    {
        /** @var ProductRepository $qb */
        $em = $this->doctrineHelper->getEntityRepositoryForClass(Product::class);
        $this->doctrineHelper
            ->getEntityManager(Product::class)
            ->getConnection()
            ->getConfiguration()
            ->setSQLLogger();

        $qb = $em->createQueryBuilder('p');
        $query = $qb->select('p.id', 'p.sku');

        return $query->getQuery()->toIterable();
    }
}
