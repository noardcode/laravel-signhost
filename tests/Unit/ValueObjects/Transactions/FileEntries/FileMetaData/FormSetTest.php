<?php

use Noardcode\LaravelSignhost\Enums\FormSetType;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\FileMetaData\FormSet;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\FileMetaData\FormSet\FieldType;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\FileMetaData\FormSet\Location;

it('can be created', function () {
    $fieldType = new FieldType(
        'Field_1',
        FormSetType::Signature,
        new Location(
            'search',
            1,
            10,
            20,
            30,
            40,
            100,
            50,
            2
        )
    );

    $formSet = new FormSet(
        'Formset_1',
        [$fieldType]
    );

    expect($formSet->getFieldTypes())->toBe([$fieldType])
        ->and($formSet->getName())->toBe('Formset_1');

    $this->assertEquals([
        'Field_1' => $fieldType->toArray(),
    ], $formSet->toArray());
});

it('cannot be created without a name', function () {
    $this->expectException(InvalidArgumentException::class);

    new FormSet(
        '',
        []
    );
});

it('cannot be created with strange characters in a name', function () {
    $this->expectException(InvalidArgumentException::class);

    new FormSet(
        'J@(*(*@H%*@H%@(*%%@*H%(',
        []
    );
});
