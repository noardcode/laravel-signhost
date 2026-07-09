<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\Collections;

use Noardcode\LaravelSignhost\Casts\Collections\TransactionVerificationsCollection;
use Noardcode\LaravelSignhost\Exceptions\SignhostException;
use Noardcode\LaravelSignhost\Tests\TestCase;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Verifications\Digid;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Verifications\Itsmesign;

class TransactionVerificationsCollectionTest extends TestCase
{
    public function test_collection_can_be_created(): void
    {
        $collection = new TransactionVerificationsCollection([
            new Digid,
            new Itsmesign,
        ]);

        $this->assertEquals(2, $collection->count());
    }

    public function test_collection_can_only_contain_instances_of_verification_interface(): void
    {
        $this->expectException(SignhostException::class);
        $this->expectExceptionMessage('Items are not an instance of VerificationInterface or not in the correct order.');

        new TransactionVerificationsCollection([
            new \Noardcode\LaravelSignhost\ValueObjects\Transactions\Authentications\Digid('111222333'),
            new Itsmesign,
        ]);
    }

    public function test_collection_items_must_be_in_right_order(): void
    {
        $this->expectException(SignhostException::class);
        $this->expectExceptionMessage('Items are not an instance of VerificationInterface or not in the correct order.');

        new TransactionVerificationsCollection([
            new Itsmesign,
            new Digid,
        ]);
    }
}
