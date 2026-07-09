<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\Collections;

use Noardcode\LaravelSignhost\Casts\Collections\TransactionFileMetaDataFormSetsCollection;
use Noardcode\LaravelSignhost\Enums\FormSetType;
use Noardcode\LaravelSignhost\Tests\TestCase;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\FileMetaData\FormSet;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\FileMetaData\FormSet\FieldType;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\FileMetaData\FormSet\Location;

class TransactionFileMetaDataFormSetsCollectionTest extends TestCase
{
    public function test_to_array_is_keyed_by_formset_name(): void
    {
        $fs1 = new FormSet('SignatureA', [
            new FieldType('Field1', FormSetType::Signature, new Location('Sign here', 1)),
        ]);
        $fs2 = new FormSet('SealBlock', [
            new FieldType('Field2', FormSetType::Seal, new Location('Seal here', 1)),
        ]);

        $collection = new TransactionFileMetaDataFormSetsCollection([$fs1, $fs2]);

        $array = $collection->toArray();

        $this->assertArrayHasKey('SignatureA', $array);
        $this->assertArrayHasKey('SealBlock', $array);
        $this->assertEquals($fs1->toArray(), $array['SignatureA']);
        $this->assertEquals($fs2->toArray(), $array['SealBlock']);
    }
}
