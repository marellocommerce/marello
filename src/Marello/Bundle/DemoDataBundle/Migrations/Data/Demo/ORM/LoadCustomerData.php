<?php

namespace Marello\Bundle\DemoDataBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Marello\Bundle\AddressBundle\Entity\MarelloAddress;
use Marello\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\AddressBundle\Entity\Country;
use Oro\Bundle\AddressBundle\Entity\Region;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

class LoadCustomerData extends AbstractFixture
{
    /** flush manager count */
    const FLUSH_MAX = 25;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $organization = $manager
            ->getRepository(Organization::class)
            ->getFirst();

        $handle = fopen($this->getDictionary('customers.csv'), "r");
        if ($handle) {
            $headers = [];
            if (($data = fgetcsv($handle, 2000, ",")) !== false) {
                //read headers
                $headers = $data;
            }
            $i = 0;
            while (($data = fgetcsv($handle, 2000, ",")) !== false) {
                $data = array_combine($headers, array_values($data));
                /** @var Customer $customer */
                $customer = $this->createCustomer($data, $organization);
                $this->setReference('marello-customer-' . $i, $customer);
                $i++;
            }
            $this->closeFiles($handle);
        }
        $this->manager->flush();
    }

    /**
     * Close all open files.
     * @param resource $handle
     */
    protected function closeFiles($handle)
    {
        if ($handle) {
            fclose($handle);
        }
    }

    /**
     * @param array        $row
     * @param Organization $organization
     *
     * @return Customer
     */
    protected function createCustomer($row, Organization $organization)
    {
        $primaryAddress = new MarelloAddress();
        $primaryAddress->setNamePrefix($row['title']);
        $primaryAddress->setFirstName($row['firstname']);
        $primaryAddress->setLastName($row['lastname']);
        if (!preg_match("/([0-9]+)/", $row['street'])) {
            $row['street'] = sprintf('%s %s', $row['street'], $this->generateRandomInt());
        }
        $primaryAddress->setStreet($row['street']);
        $primaryAddress->setPostalCode($row['zipcode']);
        $primaryAddress->setCity($row['city']);
        /** @var Country $country */
        $country = $this->manager
            ->getRepository(Country::class)
            ->findOneBy([
                'name' => $row['country']
            ]);

        $primaryAddress->setCountry($country);

        $state  = sprintf('%%%s%%', $row['state']);
        /** @var Region $region */
        $region = $this->manager
            ->getRepository(Region::class)
            ->createQueryBuilder('r')
            ->where('r.name LIKE :state')
            ->andWhere('r.country = :country')
            ->setParameter('state', $state)
            ->setParameter('country', $country->getIso2Code())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        
        if ($region) {
            $primaryAddress->setRegion($region);
        }

        $primaryAddress->setPhone($row['phone']);
        $shippingAddress = clone $primaryAddress;
        $this->manager->persist($shippingAddress);
        $this->manager->persist($primaryAddress);

        $customer = Customer::create(
            $row['firstname'],
            $row['lastname'],
            $row['email'],
            $primaryAddress,
            $shippingAddress
        );
        $customer->setNamePrefix($row['title']);
        $customer->setOrganization($organization);
        $this->manager->persist($customer);
        
        return $customer;
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

    /**
     * Generate number for street address
     * @return int
     */
    protected function generateRandomInt()
    {
        return mt_rand(1, 3000);
    }
}
