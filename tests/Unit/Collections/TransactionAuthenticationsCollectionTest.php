<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\Collections;

use Noardcode\LaravelSignhost\Casts\Collections\TransactionAuthenticationsCollection;
use Noardcode\LaravelSignhost\Contracts\AuthenticationContract;
use Noardcode\LaravelSignhost\Contracts\ToSignhostArrayContract;
use Noardcode\LaravelSignhost\Exceptions\SignhostException;
use Noardcode\LaravelSignhost\Tests\TestCase;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Authentications\Phonenumber;

class TransactionAuthenticationsCollectionTest extends TestCase
{
    public function test_empty_collection_throws_exception(): void
    {
        $this->expectException(SignhostException::class);
        $this->expectExceptionMessage('Transaction authentications collection is empty.');

        new TransactionAuthenticationsCollection([]);
    }

    public function test_collection_rejects_non_authentication_items(): void
    {
        $this->expectException(SignhostException::class);
        $this->expectExceptionMessage('Items are not an instance of AuthenticationInterface or not in the correct order.');

        new TransactionAuthenticationsCollection([
            new class
            {
                public function __toString()
                {
                    return 'not-auth';
                }
            },
            new Phonenumber('+31612345678'),
        ]);
    }

    public function test_last_item_must_be_final_authentication(): void
    {
        $this->expectException(SignhostException::class);
        $this->expectExceptionMessage('Items are not an instance of AuthenticationInterface or not in the correct order.');

        // Create an anonymous AuthenticationContract-like object that is not a final type (simulate wrong order)
        $nonFinalAuth = new class implements AuthenticationContract, ToSignhostArrayContract
        {
            public function toArray(): array
            {
                return ['Type' => 'Custom'];
            }

            public function getType(): string
            {
                return 'Custom';
            }
        };

        new TransactionAuthenticationsCollection([
            $nonFinalAuth,
        ]);
    }

    public function test_valid_collection_builds_and_serializes(): void
    {
        $collection = new TransactionAuthenticationsCollection([
            new Phonenumber('+31612345678'),
        ]);

        $this->assertCount(1, $collection);
        $this->assertSame([
            ['Type' => 'PhoneNumber', 'Number' => '+31612345678', 'SecureDownload' => false],
        ], $collection->toArray());
    }
}
