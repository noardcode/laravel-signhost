<?php

namespace Noardcode\LaravelSignhost\Casts\Collections;

use Illuminate\Support\Collection;
use Noardcode\LaravelSignhost\Contracts\ToSignhostArrayContract;

/**
 * Collection TransactionFileMetaDataSignersCollection
 *
 * Holds signer metadata configurations per file (e.g., which signers
 * are required for a particular document) and converts them for Signhost.
 *
 * @collection TransactionFileMetaDataSignersCollection
 */
class TransactionFileMetaDataSignersCollection extends Collection implements ToSignhostArrayContract
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
     * Convert the collection of Signer value objects to a Signhost-ready array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $signers = [];
        foreach ($this->items as $signer) {
            $signers[$signer->getId()] = [
                'FormSets' => $signer->getFormSets(),
            ];
        }

        return $signers;
    }
}
