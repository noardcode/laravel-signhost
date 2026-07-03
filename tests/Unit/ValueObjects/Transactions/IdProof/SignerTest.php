<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\ValueObjects\Transactions\IdProof;

use Carbon\Carbon;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Tests\TestCase;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\IdProof\Signer;

class SignerTest extends TestCase
{
    public function test_object_creation_without_authentications_and_verifications()
    {
        $expireDate = Carbon::now()->addDays(14);
        $deligateDate = Carbon::now();

        $consent = new Signer(
            'john@doe.nl',
            'e0b39ec0-e0c6-45d9-bf0d-ae8cafbe2f48',
            'Introtext',
            null,
            null,
            true,
            'https://example.com/sign',
            'https://example.com/show',
            'https://example.com/receipt',
            'Sign Request Subject',
            'Sign Request Message',
            true,
            false,
            'Delegate reason',
            'tom@doe.nl',
            'Tom Doe',
            $deligateDate,
            'https://example.com/deligate',
            Language::Dutch,
            'Scribble Name',
            14,
            $expireDate,
            '1234567890',
            'https://example.com/redirect',
            '{"key": "value"}',
            collect([
                'key' => 'value',
            ])
        );

        $this->assertEquals('john@doe.nl', $consent->getEmail());
        $this->assertEquals('e0b39ec0-e0c6-45d9-bf0d-ae8cafbe2f48', $consent->getId());
        $this->assertEquals('Introtext', $consent->getIntroText());
        $this->assertEquals(null, $consent->getAuthentications());
        $this->assertEquals(null, $consent->getVerifications());
        $this->assertTrue($consent->getSendSignRequest());
        $this->assertEquals('Sign Request Subject', $consent->getSignRequestSubject());
        $this->assertEquals('Sign Request Message', $consent->getSignRequestMessage());
        $this->assertTrue($consent->getSendSignConfirmation());
        $this->assertFalse($consent->getAllowDelegation());
        $this->assertEquals(Language::Dutch, $consent->getLanguage());
        $this->assertEquals('Scribble Name', $consent->getScribbleName());
        $this->assertEquals(14, $consent->getDaysToRemind());
        $this->assertEquals($expireDate, $consent->getExpires());
        $this->assertEquals('1234567890', $consent->getReference());
        $this->assertEquals('https://example.com/show', $consent->getShowUrl());
        $this->assertEquals('https://example.com/receipt', $consent->getReceiptUrl());
        $this->assertEquals('Delegate reason', $consent->getDelegateReason());
        $this->assertEquals('Tom Doe', $consent->getDelegateSignerName());
        $this->assertEquals('tom@doe.nl', $consent->getDelegateSignerEmail());
        $this->assertEquals($deligateDate, $consent->getSignerDelegationDateTime());
        $this->assertEquals('https://example.com/deligate', $consent->getDelegateSignUrl());
    }
}
