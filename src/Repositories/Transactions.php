<?php

namespace Noardcode\LaravelSignhost\Repositories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Noardcode\LaravelSignhost\Enums\TransactionStatus;
use Noardcode\LaravelSignhost\Enums\TransactionType;
use Noardcode\LaravelSignhost\Exceptions\SignhostException;
use Noardcode\LaravelSignhost\Models\Transaction;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileUpload as FileValueObject;

/**
 * Class Transactions
 */
class Transactions
{
    public function __construct(
        private readonly Files $files
    ) {}

    /**
     * @throws FileNotFoundException
     * @throws SignhostException
     */
    public function storeTransaction(
        mixed $data,
        null|array|FileValueObject $documents = null,
    ): Model|Builder {
        $transaction = Transaction::query()->updateOrCreate([
            'id' => $data['Id'],
        ], [
            'id' => $data['Id'],
            'type' => TransactionType::DocumentSign,
            'status' => TransactionStatus::from((int) $data['Status']),
            'seal' => $data['Seal'] ?? false,
            'reference' => $data['Reference'] ?? null,
            'postback_url' => $data['PostbackUrl'] ?? null,
            'sign_request_mode' => $data['SignRequestMode'] ?? 0,
            'days_to_expire' => $data['DaysToExpire'] ?? 60,
            'send_email_notifications' => $data['SendEmailNotifications'] ?? false,
            'context' => $data['Context'] ?? null,
            'created_date_time' => $data['CreatedDateTime'] ?? now()->toIso8601String(),
            'modified_date_time' => $data['ModifiedDateTime'] ?? now()->toIso8601String(),
            'canceled_date_time' => $data['CanceledDateTime'] ?? null,
        ])->refresh();

        $this->addReceivers($transaction, $data);
        $this->addFiles($transaction, $documents);

        return $transaction->load(['receivers', 'files']);
    }

    /**
     * Attach signers to the given transaction.
     */
    public function addReceivers(Transaction $transaction, mixed $data): void
    {
        foreach ($data['Signers'] as $signer) {
            $transaction->receivers()->updateorCreate([
                'id' => $signer['Id'],
            ], [
                'id' => $signer['Id'],
                'name' => $signer['ScribbleName'] ?? $signer['Email'],
                'email' => $signer['Email'],
                'message' => $signer['IntroText'] ?? '',
                'language' => $signer['Language'] ?? 'en-US',
                'reference' => $signer['Reference'] ?? null,
                'sign_url' => $signer['SignUrl'],
                'created_date_time' => Carbon::parse($signer['CreatedDateTime']),
                'modified_date_time' => Carbon::parse($signer['ModifiedDateTime']),
            ]);
        }
    }

    /**
     * Attach files to the transaction, persisting original files to disk first.
     *
     * @param  FileValueObject[]|null  $files
     *
     * @throws FileNotFoundException
     * @throws SignhostException
     */
    public function addFiles(Transaction $transaction, ?array $files): void
    {
        if (is_null($files)) {
            return;
        }

        foreach ($files as $file) {
            $storedPath = $this->files->storeOriginal($transaction, $file);

            $transaction->files()->create([
                'transaction_id' => $transaction->id,
                'display_name' => $file->toFileMetaData()->getDisplayName(),
                'meta_data' => $file->toFileMetaData(),
                'original_file_path' => $storedPath,
            ]);
        }
    }

    /**
     * Update the transaction's receipt path.
     */
    public function setReceiptPath(Transaction $transaction, string $receiptPath): void
    {
        $transaction->update(['receipt' => $receiptPath]);
    }

    /**
     * Mark the transaction as finalized.
     */
    public function markFinalized(Transaction $transaction): void
    {
        $transaction->update([
            'finalized' => true,
            'finalized_at' => Carbon::now(),
        ]);
    }
}
