<?php

namespace Noardcode\LaravelSignhost\Casts\Collections;

use Illuminate\Support\Collection;
use Noardcode\LaravelSignhost\Contracts\ToSignhostArrayContract;

/**
 * Collection TransactionSignersCollection
 *
 * Holds a list of Signer value objects and converts them to the
 * array structure expected by the Signhost API when required.
 *
 * @collection TransactionSignersCollection
 */
class TransactionSignersCollection extends Collection implements ToSignhostArrayContract
{
    /**
     * @param  array  $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }

    /**
     * Convert the collection of Signer value objects to a Signhost-ready array.
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
