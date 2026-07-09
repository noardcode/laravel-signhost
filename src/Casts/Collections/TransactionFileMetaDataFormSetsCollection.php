<?php

namespace Noardcode\LaravelSignhost\Casts\Collections;

use Illuminate\Support\Collection;
use Noardcode\LaravelSignhost\Contracts\ToSignhostArrayContract;

/**
 * Collection TransactionFileMetaDataFormSetsCollection
 *
 * Contains form set definitions that can be applied to a file's metadata
 * (e.g., signature, seal, checkbox). Provides array conversion keyed by name.
 *
 * @collection TransactionFileMetaDataFormSetsCollection
 */
class TransactionFileMetaDataFormSetsCollection extends Collection implements ToSignhostArrayContract
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
     * Convert the collection to an associative array keyed by form set name.
     *
     * @return array
     */
    public function toArray(): array
    {
        $formSets = [];
        foreach ($this->items as $formSet) {
            $formSets[$formSet->getName()] = $formSet->toArray();
        }

        return $formSets;
    }
}
