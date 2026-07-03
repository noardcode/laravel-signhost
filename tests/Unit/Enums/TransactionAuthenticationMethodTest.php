<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\Enums;

use Noardcode\LaravelSignhost\Enums\TransactionAuthenticationMethod;
use Noardcode\LaravelSignhost\Tests\TestCase;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Verifications\Digid;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Verifications\Phonenumber;

class TransactionAuthenticationMethodTest extends TestCase
{
    public function test_cases_and_labels(): void
    {
        $this->assertSame('DigiD', TransactionAuthenticationMethod::Digid->label());
        $this->assertSame('PhoneNumber', TransactionAuthenticationMethod::Phonenumber->label());

        $this->assertSame(Digid::class, TransactionAuthenticationMethod::Digid->value);
        $this->assertSame(Phonenumber::class, TransactionAuthenticationMethod::Phonenumber->value);
    }
}
