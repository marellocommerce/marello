<?php

namespace Marello\Bundle\CoreBundle\Model;

interface HashAwareInterface
{
    public function setVariantHash(?string $variantHash): self;

    public function getVariantHash(): string;
}
