<?php

namespace Noardcode\LaravelSignhost\Casts\Collections;

use Illuminate\Support\Collection;
use Noardcode\LaravelSignhost\Contracts\ToSignhostArrayContract;

/**
 * Collection TransactionFileEntryLinksCollection
 *
 * Holds a list of Link value objects for a transaction file entry and
 * converts them to the array structure expected by Signhost.
 *
 * @collection TransactionFileEntryLinksCollection
 */
class TransactionFileEntryLinksCollection extends Collection implements ToSignhostArrayContract
{
    /**
     * @param  array  $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }

    /**
     * Convert the collection of Link value objects to a Signhost-ready array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->map(function ($item) {
            if ($item instanceof ToSignhostArrayContract) {
                return $item->toArray();
            }

            return $item;
        })->values()->all();
    }
}
