<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\Collections;

use Noardcode\LaravelSignhost\Casts\Collections\TransactionFileEntryLinksCollection;
use Noardcode\LaravelSignhost\Enums\FileEntryLinkRel;
use Noardcode\LaravelSignhost\Tests\TestCase;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\Link;

class TransactionFileEntryLinksCollectionTest extends TestCase
{
    public function test_to_array_maps_links(): void
    {
        $links = new TransactionFileEntryLinksCollection([
            new Link(FileEntryLinkRel::File, 'application/pdf', 'https://example.com/file.pdf'),
            new Link(FileEntryLinkRel::Receipt, 'application/pdf', 'https://example.com/receipt.pdf'),
        ]);

        $this->assertSame([
            ['Rel' => 'file', 'Type' => 'application/pdf', 'Link' => 'https://example.com/file.pdf'],
            ['Rel' => 'receipt', 'Type' => 'application/pdf', 'Link' => 'https://example.com/receipt.pdf'],
        ], $links->toArray());
    }
}
