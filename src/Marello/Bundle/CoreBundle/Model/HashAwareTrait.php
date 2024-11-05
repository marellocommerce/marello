<?php

namespace Marello\Bundle\CoreBundle\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\SecurityBundle\Tools\UUIDGenerator;

trait HashAwareTrait
{
    // rename to variant_hash
    #[ORM\Column(name: 'variant_hash', type: Types::STRING, nullable: false)]
    protected ?string $variantHash = null;

    public function setVariantHash(?string $variantHash): self
    {
        $this->variantHash = $variantHash;

        return $this;
    }

    public function getVariantHash(): string
    {
        return $this->variantHash;
    }

    #[ORM\PrePersist]
    public function prePersistVariantHash()
    {
        if (!$this->variantHash) {
            $this->variantHash = md5(UUIDGenerator::v4());
        }
    }
}
