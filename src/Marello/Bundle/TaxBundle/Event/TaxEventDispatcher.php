<?php

namespace Marello\Bundle\TaxBundle\Event;

use Marello\Bundle\TaxBundle\Model\Result;
use Marello\Bundle\TaxBundle\Model\Taxable;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TaxEventDispatcher
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Taxable $taxable
     * @return Result
     */
    public function dispatch(Taxable $taxable)
    {
        $event = new ResolveTaxEvent($taxable);

        $this->eventDispatcher->dispatch($event, ResolveTaxEvent::RESOLVE_BEFORE);
        $this->eventDispatcher->dispatch($event, ResolveTaxEvent::RESOLVE);
        $this->eventDispatcher->dispatch($event, ResolveTaxEvent::RESOLVE_AFTER);

        return $taxable->getResult();
    }
}
