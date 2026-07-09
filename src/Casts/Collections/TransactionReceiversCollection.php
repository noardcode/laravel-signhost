<?php

namespace Noardcode\LaravelSignhost\Casts\Collections;

use Illuminate\Support\Collection;

/**
 * Collection TransactionReceiversCollection
 *
 * Represents the receivers for a transaction (e.g., stakeholders who
 * should receive notifications or final artifacts).
 *
 * @collection TransactionReceiversCollection
 */
class TransactionReceiversCollection extends Collection
{
    /**
     * @param  array  $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }
}
