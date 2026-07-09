<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\Collections;

use Noardcode\LaravelSignhost\Casts\Collections\TransactionSignersCollection;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Tests\TestCase;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Signer;

class TransactionSignersCollectionTest extends TestCase
{
    public function test_to_array_delegates_to_signer_value_objects(): void
    {
        $signers = new TransactionSignersCollection([
            new Signer(email: 'a@example.com', language: Language::Dutch),
            new Signer(email: 'b@example.com', language: Language::English),
        ]);

        $array = $signers->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);
        $this->assertSame('a@example.com', $array[0]['Email']);
        $this->assertSame('b@example.com', $array[1]['Email']);
    }
}
