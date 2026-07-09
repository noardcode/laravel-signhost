<?php

namespace Noardcode\LaravelSignhost\Facades\Services;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Noardcode\LaravelSignhost\Events\SignhostTransactionCreated;
use Noardcode\LaravelSignhost\Events\SignhostTransactionDeleted;
use Noardcode\LaravelSignhost\Events\SignhostTransactionStarted;
use Noardcode\LaravelSignhost\Exceptions\SignhostException;
use Noardcode\LaravelSignhost\Facades\SignhostClient;
use Noardcode\LaravelSignhost\Mappers\TransactionWebhookMapper;
use Noardcode\LaravelSignhost\Models\Activity;
use Noardcode\LaravelSignhost\Models\Transaction;
use Noardcode\LaravelSignhost\Models\Transaction as TransactionModel;
use Noardcode\LaravelSignhost\Repositories\Files as FilesRepository;
use Noardcode\LaravelSignhost\Repositories\Transactions as TransactionsRepository;
use Noardcode\LaravelSignhost\ValueObjects\Transaction as TransactionValueObject;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileUpload as FileValueObject;

/**
 * Service Signing
 *
 * High-level orchestration service for creating, uploading, starting and
 * finalizing Signhost transactions. Delegates low-level HTTP calls to
 * SignhostClient endpoints and storage/persistence to repositories.
 *
 * @service Signing
 */
class Signing
{
    /**
     * Holds the working transaction context when needed by specific operations.
     */
    protected ?Transaction $transaction = null;

    /**
     * Create a transaction on Signhost and store it locally with optional files.
     *
     * @param  TransactionValueObject  $transactionValueObject
     * @param  null|FileValueObject|FileValueObject[]  $documents
     * @return Transaction
     *
     * @throws SignhostException
     * @throws ConnectionException
     * @throws Exception
     */
    public function create(
        TransactionValueObject $transactionValueObject,
        null|array|FileValueObject $documents = null
    ): Transaction {
        if ($documents instanceof FileValueObject) {
            $documents = [$documents];
        }

        $transaction = SignhostClient::getClient()
            ->transaction
            ->createTransaction(
                $transactionValueObject,
                $documents
            );

        SignhostTransactionCreated::dispatch($transaction);

        return $transaction->refresh();
    }

    /**
     * Upload all files attached to the given transaction to Signhost.
     *
     * @param  Transaction  $transaction
     * @return Transaction
     *
     * @throws Exception
     */
    public function createFiles(
        Transaction $transaction,
    ): Transaction {
        $client = SignhostClient::getClient();

        if (! empty($transaction?->files)) {
            $filesRepo = app(FilesRepository::class);
            foreach ($transaction->files as $file) {
                $filesRepo->ensureExists($file);
                $client->transaction->file->create($transaction, $file);
            }
        }

        return $transaction->refresh();
    }

    /**
     * Start the transaction on Signhost (enables signing flow).
     *
     * @param  Transaction  $transaction
     * @return Transaction
     *
     * @throws ConnectionException
     * @throws SignhostException
     */
    public function startTransaction(
        Transaction $transaction,
    ): Transaction {
        $client = SignhostClient::getClient();
        if (! empty($transaction?->files)) {
            $client->transaction->startTransaction($transaction->id);
        }
        SignhostTransactionStarted::dispatch($transaction);

        return $transaction->refresh();
    }

    /**
     *  Get and update the transaction locally with the latest Signhost data.
     *
     * @throws SignhostException
     */
    public function updateTransaction(Transaction $transaction): TransactionModel
    {
        try {
            $collection = collect(SignhostClient::getClient()->transaction->getTransaction($transaction->id));
            $transactionVo = (new TransactionWebhookMapper)->fromCollection($collection);

            foreach ($transactionVo->getSigners() ?? [] as $signer) {
                if (empty($signer->getActivities())) {
                    continue;
                }

                foreach ($signer->getActivities() as $activity) {
                    Activity::query()->firstOrCreate([
                        'id' => $activity->getId(),
                        'transaction_id' => $transaction->id,
                    ], [
                        'transaction_signer_id' => $signer->getId(),
                        'state' => $activity->getCode(),
                        'state_code' => $activity->getCode()->value,
                        'created_at' => $activity->getCreatedDateTime(),
                    ]);
                }
            }

            $transaction->update([
                'status' => $transactionVo->getStatus()->value,
            ]);

            return $transaction->refresh();
        } catch (Exception $e) {
            throw new SignhostException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Delete the transaction remotely and locally.
     *
     * @param  Transaction  $transaction
     * @param  string  $reason
     * @return void
     *
     * @throws Exception
     */
    public function deleteTransaction(Transaction $transaction, string $reason): void
    {
        SignhostClient::getClient()->transaction->deleteTransaction($transaction, $reason);

        $transaction->delete();

        SignhostTransactionDeleted::dispatch($transaction);
    }

    /**
     * Download and persist the transaction receipt PDF.
     *
     * @param  Transaction  $transaction
     * @return void
     *
     * @throws ConnectionException|SignhostException
     */
    public function getReceipt(Transaction $transaction): void
    {
        $response = SignhostClient::getClient()->transaction->receipt->get($transaction->id);

        $filesRepo = app(FilesRepository::class);
        $txRepo = app(TransactionsRepository::class);

        $receiptPath = $filesRepo->storeReceiptFile($transaction, $response);
        $txRepo->setReceiptPath($transaction, $receiptPath);
    }

    /**
     * Download and persist all signed files for the transaction.
     *
     * @param  Transaction  $transaction
     * @return void
     *
     * @throws Exception
     */
    public function getSignedFiles(Transaction $transaction): void
    {
        if (empty($transaction->files)) {
            return;
        }

        $client = SignhostClient::getClient();
        $filesRepo = app(FilesRepository::class);
        $txRepo = app(TransactionsRepository::class);

        foreach ($transaction->files as $file) {
            $path = $filesRepo->storeSignedFile(
                $transaction,
                $client->transaction->file->get($transaction->id, $file->id),
                $file
            );

            $file->update(['signed_file_path' => $path]);
        }

        $txRepo->markFinalized($transaction);
    }
}
