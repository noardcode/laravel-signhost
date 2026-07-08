<?php

namespace Noardcode\LaravelSignhost\Facades;

use Noardcode\LaravelSignhost\Exceptions\SignhostException;
use Noardcode\LaravelSignhost\Facades\Services\IdProof;
use Noardcode\LaravelSignhost\Facades\Services\Signing;

/**
 * Service SignhostService
 *
 * Aggregates high-level package services and exposes them for the Signhost
 * facade. Also centralizes shared configuration helpers (e.g. storage disk).
 *
 * @service SignhostService
 */
class SignhostService
{
    /**
     * @return Signing
     */
    public function signing(): Signing
    {
        return app(Signing::class);
    }

    /**
     * @return IdProof
     *
     * @deprecated The IdProof feature is deprecated and will be removed in a future version.
     */
    public function idProof(): IdProof
    {
        return app(IdProof::class);
    }

    /**
     * @return string
     *
     * @throws SignhostException
     */
    public function getDisk(): string
    {
        $disk = config('signhost.disk') ?? config('filesystems.default');

        if (! is_string($disk) || empty($disk)) {
            throw new SignhostException('Disk not found or invalid');
        }

        return $disk;
    }
}
