<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\Repositories;

use Noardcode\LaravelSignhost\Models\Transaction;
use Noardcode\LaravelSignhost\Repositories\Transactions;
use Noardcode\LaravelSignhost\Tests\TestCase;

class TransactionsTest extends TestCase
{
    public function test_mark_finalized_persists_finalized_flag_and_timestamp()
    {
        $transaction = Transaction::factory()->create([
            'finalized' => false,
            'finalized_at' => null,
        ]);

        app(Transactions::class)->markFinalized($transaction);

        $fresh = $transaction->fresh();

        $this->assertTrue($fresh->finalized);
        $this->assertNotNull($fresh->finalized_at);
    }
}
