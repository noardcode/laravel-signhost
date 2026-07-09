<?php

namespace Noardcode\LaravelSignhost\Casts\Collections;

use Illuminate\Support\Collection;
use Noardcode\LaravelSignhost\Contracts\AuthenticationContract;
use Noardcode\LaravelSignhost\Exceptions\SignhostException;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Authentications\Digid;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Authentications\Phonenumber;

/**
 * Collection TransactionAuthenticationsCollection
 *
 * Represents the authentication methods required for a signer to access
 * the signing flow (e.g., DigiD or phone number). Includes validation of
 * presence and correct ordering.
 *
 * @collection TransactionAuthenticationsCollection
 */
class TransactionAuthenticationsCollection extends Collection
{
    /**
     * @throws SignhostException
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);

        $this->validate();
    }

    /**
     * @throws SignhostException
     */
    public function validate(): void
    {
        if ($this->isEmpty()) {
            throw new SignhostException('Transaction authentications collection is empty.');
        }

        // Validate the order of the items.
        if (! $this->validateItems()) {
            throw new SignhostException(
                'Items are not an instance of AuthenticationInterface or not in the correct order.'
            );
        }
    }

    /**
     * Check if all items in the collection are an instance of AuthenticationInterface and if the order of the items
     * is correct.
     *
     * @return bool
     */
    public function validateItems(): bool
    {
        foreach ($this->items as $key => $item) {
            if (! is_object($item) || $item instanceof AuthenticationContract === false) {
                return false;
            }

            $hasFinalVerificationMethod = in_array(get_class($item), [
                Digid::class,
                Phonenumber::class,
            ]);

            if (! $hasFinalVerificationMethod && $key === array_key_last($this->items)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     *
     * @throws SignhostException
     */
    public function toArray(): array
    {
        $types = [];

        foreach ($this->items as $item) {
            $types[] = $item->toArray();
        }

        return $types;
    }
}
