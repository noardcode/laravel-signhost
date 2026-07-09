<?php

use Noardcode\LaravelSignhost\Casts\Collections\TransactionFileMetaDataFormSetsCollection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionFileMetaDataSignersCollection;
use Noardcode\LaravelSignhost\Enums\FormSetType;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\FileMetaData;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\FileMetaData\FormSet;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\FileMetaData\FormSet\FieldType;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\FileMetaData\FormSet\Location;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\FileMetaData\Signer;

it('can be created', function () {
    $signers = new TransactionFileMetaDataSignersCollection([
        new Signer(
            id: 'Signer_1',
            formSets: ['Formset_1'],
        ),
    ]);

    $formsets = new TransactionFileMetaDataFormSetsCollection([
        new FormSet(
            name: 'Formset_1',
            fieldTypes: [
                new FieldType(
                    name: 'Field_1',
                    formSetType: FormSetType::Signature,
                    location: new Location(
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
                ),
            ]
        ),
    ]);

    $fileMetaData = new FileMetaData(
        1,
        'Name',
        true,
        $signers,
        $formsets
    );

    expect($fileMetaData->getDisplayOrder())->toBe(1)
        ->and($fileMetaData->getDisplayName())->toBe('Name')
        ->and($fileMetaData->getSetParaph())->toBe(true)
        ->and($fileMetaData->getSigners())->toBe($signers)
        ->and($fileMetaData->getFormSets())->toBe($formsets);

    $this->assertEquals([
        'DisplayOrder' => 1,
        'DisplayName' => 'Name',
        'SetParaph' => true,
        'Signers' => $signers->toArray(),
        'FormSets' => $formsets->toArray(),
    ], $fileMetaData->toArray());
});

it('links a signer to a formset via addSigner, so it actually gets applied', function () {
    $fileMetaData = new FileMetaData(0, 'Name', false);

    $fileMetaData->setFormSet(new FormSet('Formset_1', [
        new FieldType('Field_1', FormSetType::Signature, new Location(top: 10, left: 20, width: 100, height: 50, pageNumber: 1)),
    ]));

    $fileMetaData->addSigner(new Signer(id: 'Signer_1', formSets: ['Formset_1']));

    expect($fileMetaData->toArray()['Signers'])->toBe([
        'Signer_1' => ['FormSets' => ['Formset_1']],
    ]);
});
