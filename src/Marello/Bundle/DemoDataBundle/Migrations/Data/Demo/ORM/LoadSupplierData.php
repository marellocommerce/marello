<?php

namespace Marello\Bundle\DemoDataBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Oro\Bundle\AddressBundle\Entity\Country;
use Oro\Bundle\AddressBundle\Entity\Region;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

use Marello\Bundle\SupplierBundle\Entity\Supplier;
use Marello\Bundle\AddressBundle\Entity\MarelloAddress;

class LoadSupplierData extends AbstractFixture implements DependentFixtureInterface
{
    /** @var ObjectManager $manager */
    protected $manager;

    /**
     * @var array $data
     */
    protected $data = [
        [
            'name' => 'Quiksilver',
            'priority' => 1,
            'can_dropship' => true,
            'is_active' => true,
            'currency' => 'USD',
            'code' => 'quiksilver',
            'po_send_by' => Supplier::SEND_PO_BY_EMAIL,
            'address'=>
                [
                    'street_address' => '70 Bowman St.',
                    'zipcode' => '06074',
                    'city'=> 'South Windsor',
                    'country'=> 'US',
                    'state' => 'CT'
                ],
            'email' => 'supplier1@email.com'
        ],
        [
            'name' => 'BIC Sport North America, Inc.',
            'priority' => 2,
            'can_dropship' => false,
            'is_active' => true,
            'currency' => 'EUR',
            'code' => 'bic_sport_north_america_inc',
            'po_send_by' => Supplier::SEND_PO_MANUALLY,
            'address'=>
                [
                    'street_address' => '71 Pilgrim Avenue',
                    'zipcode' => '60185',
                    'city'=> 'West Chicago',
                    'country'=> 'US',
                    'state' => 'IL'
                ],
            'email' => 'supplier2@bicsport.com'
        ]
    ];

    /**
     * {@inheritdoc}
     * @return array
     */
    public function getDependencies()
    {
        return [
            LoadProductData::class,
            LoadProductPriceData::class
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->loadSuppliers();
    }

    /**
     * load and create Suppliers
     */
    protected function loadSuppliers()
    {
        $i = 0;

        foreach ($this->data as $values) {
            $supplier = new Supplier();
            $supplier->setName($values['name']);
            $supplier->setCode($values['code']);
            $supplier->setPriority($values['priority']);
            $supplier->setCanDropship($values['can_dropship']);
            $supplier->setIsActive($values['is_active']);
            $supplier->setEmail($values['email']);
            $supplier->setCurrency($values['currency']);
            $supplier->setPoSendBy($values['po_send_by']);

            $address = new MarelloAddress();
            $address->setStreet($values['address']['street_address']);
            $address->setPostalCode($values['address']['zipcode']);
            $address->setCity($values['address']['city']);
            $address->setCountry(
                $this->manager
                    ->getRepository(Country::class)->find($values['address']['country'])
            );
            $address->setRegion(
                $this->manager
                    ->getRepository(Region::class)
                    ->findOneBy(['combinedCode' => $values['address']['country'] . '-' . $values['address']['state']])
            );
            $this->manager->persist($address);
            $organization = $this->manager
                ->getRepository(Organization::class)
                ->getFirst();
            $supplier->setOrganization($organization);
            $supplier->setAddress($address);
            $this->manager->persist($supplier);
            $this->setReference('marello_supplier_' . $i, $supplier);
            $i++;
        }

        $this->manager->flush();
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
