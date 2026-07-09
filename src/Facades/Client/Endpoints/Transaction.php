<?php

namespace Noardcode\LaravelSignhost\Facades\Client\Endpoints;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Noardcode\LaravelSignhost\Exceptions\SignhostException;
use Noardcode\LaravelSignhost\Facades\Client\Endpoints\Transaction\File;
use Noardcode\LaravelSignhost\Facades\Client\Endpoints\Transaction\Receipt;
use Noardcode\LaravelSignhost\Facades\SignhostClient;
use Noardcode\LaravelSignhost\Models\Transaction as TransactionModel;
use Noardcode\LaravelSignhost\Repositories\Transactions;
use Noardcode\LaravelSignhost\ValueObjects\Transaction as TransactionValueObject;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileUpload as FileValueObject;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Signer;

/**
 * Endpoint Transaction
 *
 * HTTP endpoint group for Signhost transaction operations: creation, deletion,
 * retrieval, and starting. Also exposes nested endpoints for files and receipts.
 *
 * @endpoint Transaction
 */
class Transaction
{
    public File $file;

    public Receipt $receipt;

    public Transactions $transactions;

    /**
     * @param  SignhostClient  $client
     */
    public function __construct(protected SignhostClient $client)
    {
        $this->transactions = app(Transactions::class);
        $this->file = new File($this->client);
        $this->receipt = new Receipt($this->client);
    }

    /**
     * @throws ConnectionException
     * @throws SignhostException
     */
    public function createTransaction(
        TransactionValueObject $transactionValueObject,
        null|array|FileValueObject $documents = null
    ): ?object {
        $transactionSigners = [];

        $transactionValueObject?->getSigners()->each(
            function (Signer $signer) use (&$transactionSigners) {
                $transactionSigners[] = [
                    'Id' => $signer->getId(),
                    'Email' => $signer?->getEmail(),
                    'SignRequestMessage' => $signer->getSignRequestMessage() ?? 'Please sign the document.',
                    'SendSignRequest' => $signer->getSendSignRequest(),
                    'SendSignConfirmation' => $signer->getSendSignConfirmation(),
                    'ReturnUrl' => $signer->getReturnUrl(),
                    'Verifications' => $signer->getVerifications()?->toArray(),
                    'Authentications' => $signer->getAuthentications()?->toArray(),
                ];
            }
        );

        $response = Http::withHeaders($this->client->getHeaders())
            ->post($this->client->getApiUrl('/transaction'), [
                'Signers' => $transactionSigners,
                'SendEmailNotifications' => false,
                'Seal' => $transactionValueObject->getSeal(),
                'Language' => $transactionValueObject->getLanguage(),
                'DaysToExpire' => $transactionValueObject->getDaysToExpire(),
            ]);

        if ($response->failed()) {
            throw new SignhostException($response->body());
        }

        try {
            return $this->transactions->storeTransaction($response, $documents);
        } catch (Exception $e) {
            throw new SignhostException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param  TransactionModel  $transaction
     * @param  string|null  $reason
     * @return array
     *
     * @throws ConnectionException
     */
    public function deleteTransaction(TransactionModel $transaction, ?string $reason = null): array
    {
        return Http::withHeaders($this->client->getHeaders())->delete(
            $this->client->getApiUrl("/transaction/$transaction->id"), [
                'Reason' => $reason ?? 'Transaction deleted by user',
            ])->json();
    }

    /**
     * @throws ConnectionException
     */
    public function getTransaction(int|string $id): array
    {
        return Http::withHeaders($this->client->getHeaders())
            ->get($this->client->getApiUrl('/transaction/'.$id))
            ->json();
    }

    /**
     * @throws ConnectionException
     * @throws SignhostException
     */
    public function startTransaction(int|string $id): Response
    {
        $response = Http::withHeaders($this->client->getHeaders())
            ->put($this->client->getApiUrl('/transaction/'.$id.'/start'));

        if ($response->failed()) {
            throw new SignhostException($response->body());
        }

        return $response;
    }
}
