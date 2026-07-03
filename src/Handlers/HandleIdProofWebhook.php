<?php

namespace Noardcode\LaravelSignhost\Handlers;

use JsonException;
use Noardcode\LaravelSignhost\Events\SignhostIdProofCreated;
use Noardcode\LaravelSignhost\Mappers\IdProofMapper;
use Noardcode\LaravelSignhost\Repositories\IdProofs;
use Throwable;

/**
 * Handler HandleIdProofWebhook
 *
 * Processes incoming Signhost IdProof webhook payloads. The raw JSON body is
 * decoded and mapped via IdProofMapper into a value object, persisted using the
 * IdProofs repository, and finally a SignhostIdProofCreated event is dispatched.
 *
 * @handler HandleIdProofWebhook
 *
 * @deprecated The IdProof feature is deprecated and will be removed in a future version. Signhost is discontinuing IdProof support.
 */
class HandleIdProofWebhook
{
    /**
     * @param  IdProofMapper  $mapper
     * @param  IdProofs  $repository
     */
    public function __construct(
        private readonly IdProofMapper $mapper,
        private readonly IdProofs $repository
    ) {}

    /**
     * @param  string  $json
     * @return void
     *
     * @throws JsonException
     * @throws Throwable
     */
    public function __invoke(string $json): void
    {
        $collection = collect(json_decode($json, true, 20, JSON_THROW_ON_ERROR));

        $vo = $this->mapper->fromCollection($collection);

        $transaction = $this->repository->storeIdProof($vo);

        $transaction->update([
            'webhook_response' => $json,
        ]);

        event(new SignhostIdProofCreated($transaction));
    }
}
