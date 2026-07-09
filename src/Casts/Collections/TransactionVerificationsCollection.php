<?php

namespace Noardcode\LaravelSignhost\Casts\Collections;

use Illuminate\Support\Collection;
use Noardcode\LaravelSignhost\Contracts\VerificationContract;
use Noardcode\LaravelSignhost\Exceptions\SignhostException;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Verifications\Consent;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Verifications\Cscqualified;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Verifications\Itsmesign;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Verifications\Phonenumber;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Verifications\Scribble;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Verifications\SigningCertificate;

/**
 * Collection TransactionVerificationsCollection
 *
 * Specifies the verification methods required of a signer to prove identity
 * or intent (e.g., iDIN, DigiD, itsme). Includes validation of permissible
 * ordering where the final method must be a conclusive verification.
 *
 * @collection TransactionVerificationsCollection
 */
class TransactionVerificationsCollection extends Collection
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
            throw new SignhostException('Transaction verifications collection is empty.');
        }

        // Validate the order of the items.
        if (! $this->validateItems()) {
            throw new SignhostException(
                'Items are not an instance of VerificationInterface or not in the correct order.'
            );
        }
    }

    /**
     * Check if all items in the collection are an instance of VerificationInterface and if the order of the items
     * is correct. All items in the collection except the last one should NOT be of the types Consent, Itsmesign,
     * Phone number, Scribble, Signing certificate or Cscqualified.
     *
     * @return bool
     */
    public function validateItems(): bool
    {
        foreach ($this->items as $key => $item) {
            if (! is_object($item) || $item instanceof VerificationContract === false) {
                return false;
            }

            $hasFinalVerificationMethod = in_array(get_class($item), [
                Consent::class,
                Itsmesign::class,
                Phonenumber::class,
                Scribble::class,
                SigningCertificate::class,
                Cscqualified::class,
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
        $this->validate();

        $types = [];

        foreach ($this->items as $item) {
            $types[] = $item->toArray();
        }

        return $types;
    }
}
