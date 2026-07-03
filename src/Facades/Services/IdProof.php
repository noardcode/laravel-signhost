<?php

namespace Noardcode\LaravelSignhost\Facades\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Noardcode\LaravelSignhost\Exceptions\SignhostException;
use Noardcode\LaravelSignhost\Facades\SignhostClient;
use Noardcode\LaravelSignhost\Models\Transaction;
use Noardcode\LaravelSignhost\Repositories\Files as FilesRepository;
use Noardcode\LaravelSignhost\Repositories\Transactions as TransactionsRepository;

/**
 * Service IdProof
 *
 * Provides helpers for initiating the IdProof flow by redirecting users to
 * the configured Signhost IdProof form with a validated identifier.
 *
 * @service IdProof
 *
 * @deprecated The IdProof feature is deprecated and will be removed in a future version. Signhost is discontinuing IdProof support.
 */
class IdProof
{
    /**
     * Redirect to the configured Signhost IdProof form with the given identifier.
     *
     * Validates the identifier per provider constraints:
     * - Max length of 255 characters
     * - Must not contain control characters (e.g., tabs, newlines, null byte)
     * - Must not contain any of these characters: : ; * ? \\ " ' / < > |
     *
     * @param  string  $identifier
     * @return RedirectResponse|Redirector
     *
     * @throws SignhostException
     *
     * @deprecated The IdProof feature is deprecated and will be removed in a future version. Signhost is discontinuing IdProof support.
     */
    public function redirectToSignhost(string $identifier): Redirector|RedirectResponse
    {
        $this->validateIdentifier($identifier);

        $url = config('signhost.id_proof.form_url').'?q='.urlencode($identifier);

        return redirect($url);
    }

    /**
     * Validate the IdProof identifier against length and invalid characters.
     *
     * @param  string  $identifier
     * @return void
     *
     * @throws SignhostException
     */
    private function validateIdentifier(string $identifier): void
    {
        if ($identifier === '' || mb_strlen($identifier) === 0) {
            throw new SignhostException('Identifier must not be empty.');
        }

        if (mb_strlen($identifier) > 255) {
            throw new SignhostException('Identifier must not exceed 255 characters.');
        }

        if (preg_match('/[\x00-\x1F\x7F]/u', $identifier)) {
            throw new SignhostException('Identifier contains control characters which are not allowed.');
        }

        if (preg_match('/[:;\*\?\x27\\"\/<>\|]/u', $identifier)) {
            throw new SignhostException('Identifier contains invalid characters. Disallowed characters are : ; * ? \\ " \' / < > |');
        }
    }

    /**
     * Download the dossier PDF and persist it.
     *
     * @param  Transaction  $transaction
     * @param  string  $fileId
     * @return void
     *
     * @throws SignhostException
     *
     * @deprecated The IdProof feature is deprecated and will be removed in a future version. Signhost is discontinuing IdProof support.
     */
    public function getDossier(Transaction $transaction, string $fileId): void
    {
        $filesRepo = app(FilesRepository::class);
        $filesRepo->storeIdProofDocument(
            $transaction,
            SignhostClient::getClient()->transaction->file->get($transaction->id, $fileId),
            $fileId
        );
    }

    /**
     * Download and persist the transaction receipt PDF.
     *
     * @param  Transaction  $transaction
     * @return void
     *
     * @throws ConnectionException|SignhostException
     *
     * @deprecated The IdProof feature is deprecated and will be removed in a future version. Signhost is discontinuing IdProof support.
     */
    public function getReceipt(Transaction $transaction): void
    {
        $response = SignhostClient::getClient()->transaction->receipt->get($transaction->id);

        $filesRepo = app(FilesRepository::class);
        $txRepo = app(TransactionsRepository::class);

        $receiptPath = $filesRepo->storeReceiptFile($transaction, $response);
        $txRepo->setReceiptPath($transaction, $receiptPath);
    }
}
