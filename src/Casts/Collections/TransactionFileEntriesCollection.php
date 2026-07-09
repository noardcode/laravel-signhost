<?php

namespace Noardcode\LaravelSignhost\Casts\Collections;

use Illuminate\Support\Collection;
use Noardcode\LaravelSignhost\Contracts\ToSignhostArrayContract;

/**
 * Collection TransactionFileEntriesCollection
 *
 * Represents a list of transaction file entries and provides conversion
 * to the array format expected by Signhost requests/responses.
 *
 * @collection TransactionFileEntriesCollection
 */
class TransactionFileEntriesCollection extends Collection implements ToSignhostArrayContract
{
    /**
     * @param  array  $items
     */
    public function __construct(
        array $items = []
    ) {
        parent::__construct($items);
    }

    /**
     * Convert the collection of FileEntry value objects to a Signhost-ready array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->toArray();
    }
}
