<?php

namespace Marello\Component\Order\Entity\Repository;

use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;

interface OrderItemRepositoryInterface extends ObjectRepository, Selectable
{
}
