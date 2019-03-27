<?php

namespace Marello\Bundle\OrderBundle\Tests\Functional\Controller\Api\Rest;

use Marello\Bundle\OrderBundle\Entity\Customer;
use Marello\Bundle\OrderBundle\Tests\Functional\DataFixtures\LoadCustomerData;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\UserBundle\Entity\User;

class CustomerControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(
            [],
            $this->generateWsseAuthHeader()
        );

        $this->loadFixtures([
            LoadCustomerData::class
        ]);
    }


    /**
     * @test
     */
    public function testCreate()
    {
        $data = [
            'firstName' => 'John',
            'lastName'  => 'Doe',
            'email'     => 'new_customer@example.com',
            'primaryAddress'   => [
                'firstName'  => 'John',
                'lastName'   => 'Doe',
                'country'    => 'NL',
                'street'     => 'Torenallee 20',
                'city'       => 'Eindhoven',
                'region'     => 'NL-NB',
                'postalCode' => '5617 BC',
                'company'    => 'Madia Inc'
            ]
        ];

        $this->client->request(
            'POST',
            $this->getUrl('marello_customer_api_post_customer'),
            $data
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $response);
    }

    /**
     * @test
     */
    public function testCget()
    {
        $this->client->request(
            'GET',
            $this->getUrl('marello_customer_api_get_customers')
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponseStatusCodeEquals($response, Response::HTTP_OK);

        $this->assertCount(10, json_decode($response->getContent(), true));
    }

    /**
     * @test
     *
     * @depends testCreate
     */
    public function getCustomerByEmailFromApi()
    {
        $email = 'new_customer@example.com';

        $this->client->request(
            'GET',
            $this->getUrl('marello_customer_api_get_customer_by_email', [
                'email' => $email
            ])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($response, Response::HTTP_OK);

        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('customer', $decodedResponse);
        $this->assertEquals('new_customer@example.com', $decodedResponse['customer']['email']);
    }

    /**
     * @test
     */
    public function testGetNonExistingCustomerShouldReturnNotFound()
    {
        $email = 'notexisting@customer.com';

        $this->client->request(
            'GET',
            $this->getUrl('marello_customer_api_get_customer_by_email', [
                'email' => $email
            ])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($response, Response::HTTP_NOT_FOUND);

        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertArrayNotHasKey('customer', $decodedResponse);
        $this->assertArrayHasKey('message', $decodedResponse);
        $this->assertEquals('Customer with email notexisting@customer.com not found', $decodedResponse['message']);
    }

    /**
     * {@inheritdoc}
     */
    public function testUpdateCustomerById()
    {
        /** @var Customer $existingCustomer */
        $existingCustomer = $this->getReference('marello-customer-0');
        $newFirstName = 'new name';
        $newLastName = 'new last name';
        $data = [
            'firstName' => $newFirstName,
            'lastName'  => $newLastName,
            'email'     => $existingCustomer->getEmail(),
            'primaryAddress'   => [
                'firstName'  => $existingCustomer->getFirstName(),
                'lastName'   => $existingCustomer->getLastName(),
                'country'    => $existingCustomer->getPrimaryAddress()->getCountryIso2(),
                'street'     => $existingCustomer->getPrimaryAddress()->getStreet(),
                'city'       => $existingCustomer->getPrimaryAddress()->getCity(),
                'region'     => $existingCustomer->getPrimaryAddress()->getRegion()->getCombinedCode(),
                'postalCode' => $existingCustomer->getPrimaryAddress()->getPostalCode(),
                'company'    => $existingCustomer->getPrimaryAddress()->getCompany()
            ],
        ];

        $this->client->request(
            'PUT',
            $this->getUrl('marello_customer_api_put_customer', ['id' => $existingCustomer->getId()]),
            $data
        );

        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeEquals($response, Response::HTTP_NO_CONTENT);

        // check if customer data is updated
        /** @var Customer $updatedCustomer */
        $updatedCustomer = $this->getContainer()
            ->get('doctrine')
            ->getRepository('MarelloOrderBundle:Customer')
            ->find($existingCustomer->getId());

        $this->assertEquals($updatedCustomer->getEmail(), $existingCustomer->getEmail());
        $this->assertEquals($updatedCustomer->getFirstName(), $newFirstName);
        $this->assertEquals($updatedCustomer->getLastName(), $newLastName);
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        return $this->getEntityManager()->getRepository('OroUserBundle:User')->findOneByUsername(self::USER_NAME);
    }

    /**
     * @return ObjectManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }
}
