<?php

namespace Noardcode\LaravelSignhost\Casts\Collections;

use Illuminate\Support\Collection;

/**
 * Collection IdProofReceiversCollection
 *
 * Holds receiver value objects for IdProof transactions. Used to model
 * the recipients who will receive IdProof communications or results.
 *
 * @collection IdProofReceiversCollection
 */
class IdProofReceiversCollection extends Collection
{
    /**
     * @param  array  $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }
}
