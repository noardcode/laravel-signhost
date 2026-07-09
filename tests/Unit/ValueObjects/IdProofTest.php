<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\ValueObjects;

use Carbon\Carbon;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionFileEntriesCollection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionReceiversCollection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionSignersCollection;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Enums\SignRequestMode;
use Noardcode\LaravelSignhost\Enums\TransactionStatus;
use Noardcode\LaravelSignhost\Tests\TestCase;
use Noardcode\LaravelSignhost\ValueObjects\IdProof;

class IdProofTest extends TestCase
{
    public function test_object_creation()
    {
        $createdDate = Carbon::now();
        $modifiedDate = Carbon::now()->addMinutes(5);

        $idProof = new IdProof(
            'e0b39ec0-e0c6-45d9-bf0d-ae8cafbe2f48',
            new TransactionFileEntriesCollection([]),
            Language::Dutch,
            false,
            new TransactionSignersCollection([]),
            new TransactionReceiversCollection([]),
            '1234567890',
            'https://example.com/postback',
            SignRequestMode::AtOnce,
            14,
            true,
            TransactionStatus::InProgress,
            'Cancellation Reason',
            '{"key": "value"}',
            $createdDate,
            $modifiedDate,
            null
        );

        $this->assertEquals('e0b39ec0-e0c6-45d9-bf0d-ae8cafbe2f48', $idProof->getId());
        $this->assertEquals(new TransactionFileEntriesCollection([]), $idProof->getFileEntries());
        $this->assertEquals(Language::Dutch, $idProof->getLanguage());
        $this->assertEquals(false, $idProof->getSeal());
        $this->assertEquals(new TransactionSignersCollection([]), $idProof->getSigners());
        $this->assertEquals(new TransactionReceiversCollection([]), $idProof->getReceivers());
        $this->assertEquals('1234567890', $idProof->getReference());
        $this->assertEquals('https://example.com/postback', $idProof->getPostbackUrl());
        $this->assertEquals(TransactionStatus::InProgress, $idProof->getStatus());
        $this->assertEquals(14, $idProof->getDaysToExpire());
        $this->assertEquals(true, $idProof->getSendEmailNotifications());
        $this->assertEquals('Cancellation Reason', $idProof->getCancelationReason());
        $this->assertEquals('{"key": "value"}', $idProof->getContext());
        $this->assertEquals($createdDate, $idProof->getCreatedDateTime());
        $this->assertEquals($modifiedDate, $idProof->getModifiedDateTime());
        $this->assertEquals(SignRequestMode::AtOnce, $idProof->getSignRequestMode());
        $this->assertEquals(null, $idProof->getCanceledDateTime());

        $this->assertEquals([
            'Language' => Language::Dutch->value,
            'Seal' => false,
            'Signers' => [],
            'Receivers' => [],
            'Reference' => '1234567890',
            'PostbackUrl' => 'https://example.com/postback',
            'SignRequestMode' => SignRequestMode::AtOnce->value,
            'DaysToExpire' => 14,
            'SendEmailNotifications' => true,
            'Status' => TransactionStatus::InProgress->value,
            'CancelationReason' => 'Cancellation Reason',
            'Context' => '{"key": "value"}',
        ], $idProof->toArray());
    }
}
