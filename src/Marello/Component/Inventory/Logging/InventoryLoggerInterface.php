<?php

namespace Marello\Component\Inventory\Logging;

use Closure;
use Marello\Component\Inventory\InventoryItemInterface;

interface InventoryLoggerInterface
{
    const MANUAL_TRIGGER = 'manual';

    /**
     * Creates inventory log with given values, set in $setValues callback.
     * WARNING: Log is not persisted if old and new quantities are equal.
     *
     * @param InventoryItemInterface $item
     * @param string                 $trigger
     * @param Closure                $setValues Closure used to set log values. Takes InventoryLog as single parameter.
     */
    public function directLog(InventoryItemInterface $item, $trigger, Closure $setValues);

    /**
     * Logs inventory item changes based on computed doctrine change set.
     * Checks if there are any changes for all given items.
     *
     * @param InventoryItemInterface[]|InventoryItemInterface $items
     * @param string                                          $trigger
     * @param Closure|null                                    $modifyLog
     *
     * @throws \Exception
     */
    public function log($items, $trigger, Closure $modifyLog = null);
}
