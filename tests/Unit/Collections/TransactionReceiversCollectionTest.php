<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\Collections;

use Noardcode\LaravelSignhost\Casts\Collections\TransactionReceiversCollection;
use Noardcode\LaravelSignhost\Tests\TestCase;

class TransactionReceiversCollectionTest extends TestCase
{
    public function test_collection_can_be_instantiated_and_counted(): void
    {
        $collection = new TransactionReceiversCollection([]);
        $this->assertCount(0, $collection);

        $collection->push('receiver-1');
        $collection->push('receiver-2');

        $this->assertCount(2, $collection);
    }
}
