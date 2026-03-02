<?php

namespace Marello\Bundle\AddressBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Oro\Bundle\LocaleBundle\Model\AddressInterface;
use Oro\Bundle\LocaleBundle\Formatter\AddressFormatter;

use Marello\Bundle\AddressBundle\Entity\MarelloAddress;
use Marello\Bundle\AddressBundle\Entity\MarelloTypedAddress;

class AddressExtension extends AbstractExtension
{
    /**
     * @var AddressFormatter
     */
    private $addressFormatter;

    /**
     * @param AddressFormatter $addressFormatter
     */
    public function __construct(AddressFormatter $addressFormatter)
    {
        $this->addressFormatter = $addressFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter(
                'marello_format_address',
                [$this, 'formatAddress'],
                ['is_safe' => ['html']]
            )
        ];
    }

    /**
     * Formats address according to locale settings.
     *
     * @param                  $address
     * @param string|null      $country
     * @param string           $newLineSeparator
     *
     * @return string
     */
    public function formatAddress($address, $country = null, $newLineSeparator = "\n")
    {
        if ($address instanceof MarelloTypedAddress) {
            // tmp copy typed address into Address
            $address = $this->copyAddress($address);
        }
        return $this->addressFormatter->format($address, $country, $newLineSeparator);
    }

    private function copyAddress(MarelloTypedAddress $typedAddress)
    {
        $address = new MarelloAddress();
        $address
            ->setCountry($typedAddress->getCountry())
            ->setRegion($typedAddress->getRegion())
            ->setNamePrefix($typedAddress->getNamePrefix())
            ->setFirstName($typedAddress->getFirstName())
            ->setMiddleName($typedAddress->getMiddleName())
            ->setLastName($typedAddress->getLastName())
            ->setNameSuffix($typedAddress->getNameSuffix())
            ->setCity($typedAddress->getCity())
            ->setPostalCode($typedAddress->getPostalCode())
            ->setStreet($typedAddress->getStreet())
            ->setStreet2($typedAddress->getStreet2())
            ->setPhone($typedAddress->getPhone())
            ->setCompany($typedAddress->getCompany());

        return $address;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'marello_address';
    }
}
