<?php

namespace Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries;

use Noardcode\LaravelSignhost\Casts\Collections\TransactionFileMetaDataFormSetsCollection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionFileMetaDataSignersCollection;
use Noardcode\LaravelSignhost\Contracts\ToSignhostArrayContract;

/**
 * Class FileMetaData
 */
class FileMetaData implements ToSignhostArrayContract
{
    /**
     * @param  int  $displayOrder  With what order number we'll display the file to the signer.
     * @param  string  $displayName  With what name we'll display the file to the signer.
     * @param  bool  $setParaph  Places a copy of the signer's scribble image on the bottom right of every page when no signature is present.
     * @param  TransactionFileMetaDataSignersCollection|null  $signers  The signers that are allowed to sign this file. Each signer must be a valid Signer reference.
     * @param  TransactionFileMetaDataFormSetsCollection|null  $formSets
     */
    public function __construct(
        protected int $displayOrder,
        protected string $displayName,
        protected bool $setParaph,
        protected ?TransactionFileMetaDataSignersCollection $signers = null,
        protected ?TransactionFileMetaDataFormSetsCollection $formSets = null
    ) {
        //
    }

    /**
     * @return int
     */
    public function getDisplayOrder(): int
    {
        return $this->displayOrder;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return bool
     */
    public function getSetParaph(): bool
    {
        return $this->setParaph;
    }

    /**
     * @return TransactionFileMetaDataSignersCollection|null
     */
    public function getSigners(): ?TransactionFileMetaDataSignersCollection
    {
        return $this->signers;
    }

    /**
     * @return TransactionFileMetaDataFormSetsCollection|null
     */
    public function getFormSets(): ?TransactionFileMetaDataFormSetsCollection
    {
        return $this->formSets;
    }

    public function toArray(): array
    {
        return [
            'DisplayOrder' => $this->getDisplayOrder(),
            'DisplayName' => $this->getDisplayName(),
            'SetParaph' => $this->getSetParaph(),
            'Signers' => $this->getSigners()?->toArray(),
            'FormSets' => $this->getFormSets()?->toArray(),
        ];
    }

    public function setFormSet(FileMetaData\FormSet $formSet): ?TransactionFileMetaDataFormSetsCollection
    {
        if ($this->formSets === null) {
            $this->formSets = new TransactionFileMetaDataFormSetsCollection;
        }
        $this->formSets->add($formSet);

        return $this->formSets;
    }

    /**
     * Links a signer to one or more FormSets by name, so SignHost actually
     * renders that signer's form fields on this file. Without this, a
     * FormSet defined via `setFormSet()` is accepted by the API but never
     * applied — SignHost falls back to its default field placement.
     */
    public function addSigner(FileMetaData\Signer $signer): TransactionFileMetaDataSignersCollection
    {
        if ($this->signers === null) {
            $this->signers = new TransactionFileMetaDataSignersCollection;
        }
        $this->signers->push($signer);

        return $this->signers;
    }
}
