<?php

namespace Marello\Bundle\NotificationBundle\Model;

use Oro\Bundle\ActivityBundle\Model\ActivityInterface;
use Oro\Bundle\ActivityBundle\Model\ExtendActivity;

class ExtendNotification implements ActivityInterface
{
    use ExtendActivity;

    /**
     * Constructor
     *
     * The real implementation of this method is auto generated.
     *
     * IMPORTANT: If the derived class has own constructor it must call parent constructor.
     */
    public function __construct()
    {
    }
}
