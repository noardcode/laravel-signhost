<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\Collections;

use Noardcode\LaravelSignhost\Casts\Collections\IdProofReceiversCollection;
use Noardcode\LaravelSignhost\Tests\TestCase;

class IdProofReceiversCollectionTest extends TestCase
{
    public function test_collection_can_be_instantiated_and_counted(): void
    {
        $collection = new IdProofReceiversCollection([]);
        $this->assertCount(0, $collection);

        $collection->push('receiver-1');
        $collection->push('receiver-2');

        $this->assertCount(2, $collection);
    }
}
