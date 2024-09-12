<?php

namespace Marello\Bundle\CustomerBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Marello\Bundle\AddressBundle\Entity\MarelloAddress;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\RefundBundle\Entity\Refund;
use Marello\Bundle\CustomerBundle\Entity\Customer;
use Marello\Bundle\PackingBundle\Entity\PackingSlip;
use Marello\Bundle\InvoiceBundle\Entity\AbstractInvoice;

class MergeDuplicateCustomerData extends AbstractFixture
{

    /** @var ObjectManager $manager */
    protected $manager;

    /**
     * @var string[]
     */
    protected $classNames = [
        Order::class,
        AbstractInvoice::class,
        PackingSlip::class,
        Refund::class,
        MarelloAddress::class
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $qb = $manager->getRepository(Customer::class)->createQueryBuilder('u');
        $duplicates = $qb->select('u.emailLowercase')
            ->groupBy('u.emailLowercase')
            ->having($qb->expr()->gt('COUNT(u.id)', 1))
            ->getQuery()
            ->getArrayResult();

        foreach ($duplicates as $x => $item) {
            $result = $manager
                ->getRepository(Customer::class)
                ->findBy(
                    ['emailLowercase' => $item['emailLowercase']],
                    ['id' => 'ASC']
                );

            /** @var Customer $originalCustomer */
            $originalCustomer = array_shift($result);
            foreach ($this->classNames as $fullQualifiedClassNames) {
                $this->moveCustomers($fullQualifiedClassNames, $originalCustomer, $result);
            }

            foreach ($result as $oldEntity) {
                $originalCustomer->addAddress($oldEntity->getPrimaryAddress());
                $originalCustomer->addAddress($oldEntity->getShippingAddress());
                $oldEntity->setIsHidden(true);
                $this->manager->persist($originalCustomer);
                $this->manager->persist($oldEntity);
            }
        }

        $this->manager->flush();
    }

    /**
     * @param $className
     * @param $originalCustomer
     * @param array $additionalCustomers
     * @return void
     */
    protected function moveCustomers($className, $originalCustomer, array $additionalCustomers)
    {
        $repo = $this->manager->getRepository($className);
        foreach($additionalCustomers as $additionalCustomer) {
            $entities = $repo->findBy(['customer' => $additionalCustomer]);
            foreach ($entities as $entity) {
                $entity->setCustomer($originalCustomer);
                $this->manager->persist($entity);
            }
        }
    }
}
