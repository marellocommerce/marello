<?php

namespace Marello\Bundle\TaxBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;

use Marello\Bundle\TaxBundle\Entity\Repository\TaxCodeRepository;

/**
 * TaxCode
 */
#[ORM\Table(name: 'marello_tax_tax_code')]
#[ORM\UniqueConstraint(name: 'marello_tax_code_codeidx', columns: ['code'])]
#[ORM\Entity(repositoryClass: TaxCodeRepository::class)]
#[Oro\Config(
    routeName: 'marello_tax_taxcode_index',
    routeView: 'marello_tax_taxcode_view',
    routeUpdate: 'marello_tax_taxcode_update',
    defaultValues: [
        'dataaudit' => ['auditable' => true],
        'security' => ['type' => 'ACL', 'group_name' => '']
    ]
)]
class TaxCode implements ExtendEntityInterface
{
    use ExtendEntityTrait;

    /**
     * @var integer
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'code', type: Types::STRING, length: 255, unique: true, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['identity' => true]])]
    protected $code;

    /**
     * @var string
     */
    #[ORM\Column(name: 'description', type: Types::STRING, length: 255, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $description;

    /**
     * @var array $data
     */
    #[ORM\Column(name: 'data', type: Types::JSON, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected $data = [];

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return TaxCode
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return TaxCode
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param array $data
     * @return TaxCode
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
