<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\ValueObjects\Transactions;

use Carbon\Carbon;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Exceptions\SignhostException;
use Noardcode\LaravelSignhost\Tests\TestCase;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Receiver;

class ReceiverTest extends TestCase
{
    public function test_object_creation()
    {

        $createdDate = Carbon::now();
        $modifiedDate = Carbon::now()->addMinutes(5);

        $consent = new Receiver(
            'John Doe',
            'john@doe.nl',
            Language::Dutch,
            'Subject',
            'Message',
            '1234567890',
            '{"key": "value"}',
            'e0b39ec0-e0c6-45d9-bf0d-ae8cafbe2f48',
            $createdDate,
            $modifiedDate,
        );

        $this->assertEquals('John Doe', $consent->getName());
        $this->assertEquals('john@doe.nl', $consent->getEmail());
        $this->assertEquals(Language::Dutch, $consent->getLanguage());
        $this->assertEquals('Subject', $consent->getSubject());
        $this->assertEquals('Message', $consent->getMessage());
        $this->assertEquals('1234567890', $consent->getReference());
        $this->assertEquals('{"key": "value"}', $consent->getContext());
        $this->assertEquals('e0b39ec0-e0c6-45d9-bf0d-ae8cafbe2f48', $consent->getId());
        $this->assertEquals($createdDate, $consent->getCreatedDateTime());
        $this->assertEquals($modifiedDate, $consent->getModifiedDateTime());

        $this->assertEquals([
            'Name' => 'John Doe',
            'Email' => 'john@doe.nl',
            'Language' => Language::Dutch->value,
            'Subject' => 'Subject',
            'Message' => 'Message',
            'Reference' => '1234567890',
            'Context' => '{"key": "value"}',
        ], $consent->toArray());
    }

    public function test_object_creation_without_subject()
    {

        $createdDate = Carbon::now();
        $modifiedDate = Carbon::now()->addMinutes(5);

        $consent = new Receiver(
            'John Doe',
            'john@doe.nl',
            Language::Dutch,
            null,
            'Message',
            '1234567890',
            '{"key": "value"}',
            'e0b39ec0-e0c6-45d9-bf0d-ae8cafbe2f48',
            $createdDate,
            $modifiedDate,
        );

        $this->assertEquals(null, $consent->getSubject());
    }

    public function test_object_creation_with_too_long_subject()
    {
        $this->expectException(SignhostException::class);

        $createdDate = Carbon::now();
        $modifiedDate = Carbon::now()->addMinutes(5);

        new Receiver(
            'John Doe',
            'john@doe.nl',
            Language::Dutch,
            'LongSubjectLongSubjectLongSubjectLongSubjectLongSubjectLongSubjectLongSubjectLongSubjectLongSubjectLongSubjectLongSubjectLongSubjectLongSubjectLongSubjectLongSubject',
            'Message',
            '1234567890',
            '{"key": "value"}',
            'e0b39ec0-e0c6-45d9-bf0d-ae8cafbe2f48',
            $createdDate,
            $modifiedDate,
        );
    }
}
