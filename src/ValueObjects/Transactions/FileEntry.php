<?php

namespace Noardcode\LaravelSignhost\ValueObjects\Transactions;

use Noardcode\LaravelSignhost\Casts\Collections\TransactionFileEntryLinksCollection;
use Noardcode\LaravelSignhost\Contracts\ToSignhostArrayContract;

/**
 * Class FileEntry
 */
class FileEntry implements ToSignhostArrayContract
{
    /**
     * @param  string  $displayName
     * @param  TransactionFileEntryLinksCollection  $links
     */
    public function __construct(
        protected string $displayName,
        protected TransactionFileEntryLinksCollection $links,
    ) {
        $this->validate();
    }

    /**
     * @return void
     */
    private function validate(): void
    {
        $this->validateDisplayName();
    }

    /**
     * @return void
     */
    private function validateDisplayName(): void
    {
        if (empty($this->displayName)) {
            throw new \InvalidArgumentException('Display name is required.');
        }

        if (strlen($this->displayName) > 255) {
            throw new \InvalidArgumentException('Display name is too long (max 255 characters).');
        }
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return TransactionFileEntryLinksCollection
     */
    public function getLinks(): TransactionFileEntryLinksCollection
    {
        return $this->links;
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'DisplayName' => $this->getDisplayName(),
            'Links' => $this->getLinks()->toArray(),
        ];
    }
}
