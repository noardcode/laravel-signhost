<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\ValueObjects\Transactions;

use Carbon\Carbon;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionAuthenticationsCollection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionVerificationsCollection;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Tests\TestCase;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Authentications\Phonenumber;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Signer;

class SignerTest extends TestCase
{
    public function test_object_creation_without_authentications_and_verifications()
    {
        $expireDate = Carbon::now()->addDays(14);

        $consent = new Signer(
            'john@doe.nl',
            'e0b39ec0-e0c6-45d9-bf0d-ae8cafbe2f48',
            'Introtext',
            null,
            null,
            true,
            'Sign Request Subject',
            'Sign Request Message',
            true,
            false,
            Language::Dutch,
            'Scribble Name',
            14,
            $expireDate,
            '1234567890',
            'https://example.com/redirect',
            '{"key": "value"}',
        );

        $this->assertEquals('john@doe.nl', $consent->getEmail());
        $this->assertEquals('e0b39ec0-e0c6-45d9-bf0d-ae8cafbe2f48', $consent->getId());
        $this->assertEquals('Introtext', $consent->getIntroText());
        $this->assertEquals(null, $consent->getAuthentications());
        $this->assertEquals(null, $consent->getVerifications());
        $this->assertEquals(true, $consent->getSendSignRequest());
        $this->assertEquals('Sign Request Subject', $consent->getSignRequestSubject());
        $this->assertEquals('Sign Request Message', $consent->getSignRequestMessage());
        $this->assertEquals(true, $consent->getSendSignConfirmation());
        $this->assertEquals(false, $consent->getAllowDelegation());
        $this->assertEquals(Language::Dutch, $consent->getLanguage());
        $this->assertEquals('Scribble Name', $consent->getScribbleName());
        $this->assertEquals(14, $consent->getDaysToRemind());
        $this->assertEquals($expireDate, $consent->getExpires());
        $this->assertEquals('1234567890', $consent->getReference());
        $this->assertEquals('https://example.com/redirect', $consent->getReturnUrl());
        $this->assertEquals('{"key": "value"}', $consent->getContext());

        $this->assertEquals([
            'Id' => 'e0b39ec0-e0c6-45d9-bf0d-ae8cafbe2f48',
            'Email' => 'john@doe.nl',
            'IntroText' => 'Introtext',
            'Authentications' => null,
            'Verifications' => null,
            'SendSignRequest' => true,
            'SignRequestSubject' => 'Sign Request Subject',
            'SignRequestMessage' => 'Sign Request Message',
            'SendSignConfirmation' => true,
            'AllowDelegation' => false,
            'Language' => 'nl-NL',
            'ScribbleName' => 'Scribble Name',
            'DaysToRemind' => 14,
            'Expires' => $expireDate,
            'Reference' => '1234567890',
            'ReturnUrl' => 'https://example.com/redirect',
            'Context' => '{"key": "value"}',
        ], $consent->toArray());
    }

    public function test_object_creation_with_authentications_array()
    {
        $expireDate = Carbon::now()->addDays(14);

        $authentications = [
            new Phonenumber('+31612345678', true),
        ];

        $consent = new Signer(
            'john@doe.nl',
            'e0b39ec0-e0c6-45d9-bf0d-ae8cafbe2f48',
            'Introtext',
            $authentications,
            null,
            true,
            'Sign Request Subject',
            'Sign Request Message',
            true,
            false,
            Language::Dutch,
            'Scribble Name',
            14,
            $expireDate,
            '1234567890',
            'https://example.com/redirect',
            '{"key": "value"}',
        );

        $this->assertEquals(new TransactionAuthenticationsCollection($authentications), $consent->getAuthentications());
    }

    public function test_object_creation_with_authentications_object()
    {
        $expireDate = Carbon::now()->addDays(14);

        $authentications = new TransactionAuthenticationsCollection([
            new Phonenumber('+31612345678', true),
        ]);

        $consent = new Signer(
            'john@doe.nl',
            'e0b39ec0-e0c6-45d9-bf0d-ae8cafbe2f48',
            'Introtext',
            $authentications,
            null,
            true,
            'Sign Request Subject',
            'Sign Request Message',
            true,
            false,
            Language::Dutch,
            'Scribble Name',
            14,
            $expireDate,
            '1234567890',
            'https://example.com/redirect',
            '{"key": "value"}',
        );

        $this->assertEquals($authentications, $consent->getAuthentications());
    }

    public function test_object_creation_with_verifications_array()
    {
        $expireDate = Carbon::now()->addDays(14);

        $verifications = [
            new \Noardcode\LaravelSignhost\ValueObjects\Transactions\Verifications\Phonenumber('+31612345678'),
        ];

        $consent = new Signer(
            email: 'john@doe.nl',
            id: 'e0b39ec0-e0c6-45d9-bf0d-ae8cafbe2f48',
            introText: 'Introtext',
            authentications: null,
            verifications: $verifications,
            sendSignRequest: true,
            signRequestSubject: 'Sign Request Subject',
            signRequestMessage: 'Sign Request Message',
            sendSignConfirmation: true,
            allowDelegation: false,
            language: Language::Dutch,
            scribbleName: 'Scribble Name',
            daysToRemind: 14,
            expires: $expireDate,
            reference: '1234567890',
            returnUrl: 'https://example.com/redirect',
            context: '{"key": "value"}',
        );

        $this->assertEquals(new TransactionVerificationsCollection($verifications), $consent->getVerifications());
    }

    public function test_object_creation_with_verifications_object()
    {
        $expireDate = Carbon::now()->addDays(14);

        $verifications = new TransactionVerificationsCollection([
            new \Noardcode\LaravelSignhost\ValueObjects\Transactions\Verifications\Phonenumber('+31612345678'),
        ]);

        $consent = new Signer(
            email: 'john@doe.nl',
            id: 'e0b39ec0-e0c6-45d9-bf0d-ae8cafbe2f48',
            introText: 'Introtext',
            authentications: null,
            verifications: $verifications,
            sendSignRequest: true,
            signRequestSubject: 'Sign Request Subject',
            signRequestMessage: 'Sign Request Message',
            sendSignConfirmation: true,
            allowDelegation: false,
            language: Language::Dutch,
            scribbleName: 'Scribble Name',
            daysToRemind: 14,
            expires: $expireDate,
            reference: '1234567890',
            returnUrl: 'https://example.com/redirect',
            context: '{"key": "value"}',
        );

        $this->assertEquals($verifications, $consent->getVerifications());
    }
}
