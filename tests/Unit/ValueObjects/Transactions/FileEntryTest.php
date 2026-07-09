<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\ValueObjects\Transactions;

use Illuminate\Support\Str;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionFileEntryLinksCollection;
use Noardcode\LaravelSignhost\Enums\FileEntryLinkRel;
use Noardcode\LaravelSignhost\Tests\TestCase;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\Link;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntry;

class FileEntryTest extends TestCase
{
    public function test_object_creation()
    {
        $linksCollection = new TransactionFileEntryLinksCollection([
            new Link(
                FileEntryLinkRel::File,
                'application/pdf',
                'https://example.com/test.pdf'
            ),
        ]);

        $fileEntry = new FileEntry(
            'test.pdf',
            $linksCollection,
        );

        $this->assertEquals('test.pdf', $fileEntry->getDisplayName());
        $this->assertEquals($linksCollection, $fileEntry->getLinks());

        $this->assertEquals([
            'DisplayName' => 'test.pdf',
            'Links' => [
                [
                    'Rel' => 'file',
                    'Type' => 'application/pdf',
                    'Link' => 'https://example.com/test.pdf',
                ],
            ],
        ], $fileEntry->toArray());
    }

    public function test_object_creation_without_display_name()
    {
        $this->expectException(\InvalidArgumentException::class);

        $linksCollection = new TransactionFileEntryLinksCollection([
            new Link(
                FileEntryLinkRel::File,
                'application/pdf',
                'https://example.com/test.pdf'
            ),
        ]);

        $fileEntry = new FileEntry(
            '',
            $linksCollection,
        );
    }

    public function test_object_creation_with_too_long_display_name()
    {
        $this->expectException(\InvalidArgumentException::class);

        $linksCollection = new TransactionFileEntryLinksCollection([
            new Link(
                FileEntryLinkRel::File,
                'application/pdf',
                'https://example.com/test.pdf'
            ),
        ]);

        $fileEntry = new FileEntry(
            Str::random(256).'.pdf',
            $linksCollection,
        );
    }
}
