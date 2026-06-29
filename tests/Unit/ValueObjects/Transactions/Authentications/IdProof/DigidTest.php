<?php

use Noardcode\LaravelSignhost\Enums\ReliabilityLevel;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Authentications\IdProof\Digid;

it('returns the correct type', function () {
    $digid = new Digid(
        '111222333',
        true,
        ReliabilityLevel::High
    );
    expect($digid->getType())->toBe('DigiD')
        ->and($digid->getReliabilityLevel())->toBe(ReliabilityLevel::High);
});
