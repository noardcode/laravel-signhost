<?php

use Noardcode\LaravelSignhost\Enums\FileEntryLinkRel;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\Link;

it('can be created', function () {
    $link = new Link(
        FileEntryLinkRel::File,
        'application/pdf',
        'https://example.com/test.pdf'
    );
    expect($link->getRel())->toBe(FileEntryLinkRel::File)
        ->and($link->getType())->toBe('application/pdf')
        ->and($link->getLink())->toBe('https://example.com/test.pdf');

    $this->assertEquals([
        'Rel' => FileEntryLinkRel::File->value,
        'Type' => 'application/pdf',
        'Link' => 'https://example.com/test.pdf',
    ], $link->toArray());
});
