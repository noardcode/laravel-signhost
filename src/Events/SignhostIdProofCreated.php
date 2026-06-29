<?php

namespace Noardcode\LaravelSignhost\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Noardcode\LaravelSignhost\Models\Transaction;

/**
 * Event SignhostIdProofCreated
 *
 * Fired after the IdProof postback has been processed and the related
 * transaction has been stored/updated in the database.
 *
 * @event SignhostIdProofCreated
 *
 * @deprecated The IdProof feature is deprecated and will be removed in a future version. Signhost is discontinuing IdProof support.
 */
class SignhostIdProofCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Transaction $transaction,
    ) {}
}
