<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionSignersCollection;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Facades\Signhost;
use Noardcode\LaravelSignhost\Facades\SignhostClient;
use Noardcode\LaravelSignhost\ValueObjects\Transaction;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileUpload;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Signer;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Verifications\Scribble;

Route::get('/signhost-test', function () {
    $signers = new TransactionSignersCollection([
        new Signer(
            email: 'john@example.com',
            verifications: [
                new Scribble(
                    requireHandsignature: true,
                    scribbleNameFixed: true,
                    scribbleName: 'John Doe'
                ),
            ],
            language: Language::Dutch,
            scribbleName: 'John Doe'
        ),
    ]);

    $transaction = new Transaction(
        language: Language::Dutch,
        seal: false,
        signers: $signers,
        receivers: null,
        reference: 'agreement-2025-001',
        sendEmailNotifications: false,
    );

    $localPath = Storage::disk('local')->path('dummy.pdf');
    $file = new FileUpload($localPath, 'unsigned.pdf');

    // 1) Create the transaction in Signhost
    $storedTransaction = Signhost::signing()->create($transaction, $file);

    // 2) Upload file(s) to Signhost
    $storedTransaction = Signhost::signing()->createFiles($storedTransaction);

    // 3) Start the transaction – this triggers sending of the signing link
    Signhost::signing()->startTransaction($storedTransaction);

    // 1) After the signer completes, download the signed document(s)
    Signhost::signing()->getSignedFiles($storedTransaction);

    // 2) Download the signing receipt for verification
    Signhost::signing()->getReceipt($storedTransaction);

    // Example: fetch a transaction (replace with actual transaction ID if needed)
    //    $transactionId = '2085b053-69b5-49d1-a1f7-bac4f26e6732';
    $transactionId = $storedTransaction->id;
    $response = SignhostClient::getClient()->transaction->getTransaction($transactionId);

    return response()->json($response);
});
