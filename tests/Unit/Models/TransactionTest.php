<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\Models;

use Noardcode\LaravelSignhost\Enums\TransactionStatus;
use Noardcode\LaravelSignhost\Models\Transaction;
use Noardcode\LaravelSignhost\Tests\TestCase;

class TransactionTest extends TestCase
{
    public function test_status_column_is_cast_to_transaction_status_enum()
    {
        $transaction = Transaction::factory()->create(['status' => TransactionStatus::Signed->value]);

        $this->assertInstanceOf(TransactionStatus::class, $transaction->fresh()->status);
        $this->assertSame(TransactionStatus::Signed, $transaction->fresh()->status);
    }
}
