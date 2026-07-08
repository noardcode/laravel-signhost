<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\ValueObjects;

use Noardcode\LaravelSignhost\Collections\TransactionReceiversCollection;
use Noardcode\LaravelSignhost\Collections\TransactionSignersCollection;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Enums\SignRequestMode;
use Noardcode\LaravelSignhost\Enums\TransactionStatus;
use Noardcode\LaravelSignhost\Tests\TestCase;
use Noardcode\LaravelSignhost\ValueObjects\Transaction;

class TransactionTest extends TestCase
{
    public function test_object_creation()
    {
        $transaction = new Transaction(
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
            '{"key": "value"}'
        );

        $this->assertEquals(Language::Dutch, $transaction->getLanguage());
        $this->assertEquals(false, $transaction->getSeal());
        $this->assertEquals(new TransactionSignersCollection([]), $transaction->getSigners());
        $this->assertEquals(new TransactionReceiversCollection([]), $transaction->getReceivers());
        $this->assertEquals('1234567890', $transaction->getReference());
        $this->assertEquals('https://example.com/postback', $transaction->getPostbackUrl());
        $this->assertEquals(TransactionStatus::InProgress, $transaction->getStatus());
        $this->assertEquals(14, $transaction->getDaysToExpire());
        $this->assertEquals(true, $transaction->getSendEmailNotifications());
        $this->assertEquals('Cancellation Reason', $transaction->getCancelationReason());
        $this->assertEquals('{"key": "value"}', $transaction->getContext());
        $this->assertEquals(SignRequestMode::AtOnce, $transaction->getSignRequestMode());

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
        ], $transaction->toArray());
    }

    public function test_default_postback_url_resolves_to_registered_route()
    {
        $transaction = new Transaction(
            Language::Dutch,
            false,
            new TransactionSignersCollection([]),
            new TransactionReceiversCollection([]),
            '1234567890',
        );

        $this->assertSame(
            route('laravel-signhost.postback.transaction'),
            $transaction->getPostbackUrl()
        );
    }
}
