[![CI](https://github.com/noardcode/laravel-signhost/actions/workflows/ci.yml/badge.svg)](https://github.com/noardcode/laravel-signhost/actions/workflows/ci.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/noardcode/laravel-signhost.svg?style=flat-square)](https://packagist.org/packages/noardcode/laravel-signhost)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)
![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue?style=flat-square)
![Laravel Version](https://img.shields.io/badge/Laravel-11-orange?style=flat-square)

# Laravel Signhost

**Laravel Signhost** is the official Laravel package for integrating [Signhost](https://www.signhost.com/) document signing and digital identification into your Laravel project. It is maintained and supported by [NoardCode](https://www.noardcode.nl/).

This package provides a clean, expressive API for interacting with the Signhost signing and identification platform directly from your Laravel applications.

---

## Table of Contents

- [Electronic Signature](#electronic-signature)
- [ID Proof](#digital-identification-idproof)
- [Requirements](#requirements)
- [Getting Started with Laravel Signhost](#getting-started-with-laravel-signhost)
    - [Simulation Mode](#simulation-mode-no-account-required)
    - [Moving to a Real Signhost Account](#moving-to-a-real-signhost-account)
    - [Enabling Digital Identification (IdProof)](#enabling-digital-identification-idproof)
- [Installation](#installation)
- [Database Migrations](#database-migrations)
- [Example Usage: Signing Workflows](#example-usage-signing-workflows)
  -   [Creating a basic transaction for a signature](#creating-a-basic-transaction-for-a-signature)
  -   [Using FormSets to create a more advanced transaction](#using-formsets-to-create-a-more-advanced-transaction)
  -   [Storing document and receipt from a finished transaction](#storing-document-and-receipt-from-a-finished-transaction)
- [Example Usage: IdProof Workflows](#example-usage-idproof-workflows)
  -   [Digital Identification (IdProof)](#digital-identification-idproof)
    - [Handling the Postback](#handling-the-postback)
    - [Retrieving the Dossier documents](#retrieving-the-dossier-documents)
- [Using simulation mode](#using-simulation-mode)
  - [Simulating a Signing Activity Webhook](#simulating-a-signing-activity-webhook)
  - [Simulating a Signhost ID Proof Webhook](#simulating-a-signhost-id-proof-webhook)
- [Events](#events)
- [Storage Structure](#storage-structure)
- [Contributing](#contributing)
- [Security](#security-vulnerabilities)
- [License](#license)
- [Support](#support)

---

## Electronic Signature

[Signhost Electronic Signature](https://www.signhost.com/products/electronic-signature) enables you to sign documents online in a legally binding and compliant way.  

Documents can be digitally signed by one or multiple parties using a variety of verification methods, ensuring integrity and non-repudiation.

**Key features include:**
- Legally binding digital signatures compliant with **eIDAS** and other Global standards  
- Support for multiple signers and workflows  
- Seamless integration with popular services such as **iDEAL** (which becomes [Wero](https://wero-wallet.eu) in 2027), **SMS**, or **email verification**

> **Please Note:**  
> Signhost only stores your signed files for 30 days. After that, they are deleted and can no longer be downloaded. Make sure to store your files in a secure location.

---

## ID Proof


Beyond digital signing, Signhost offers [ID Proof](https://www.signhost.com/products/digital-identification), a solution for comprehensive digital identity verification. This package supports the verification of Passports, ID Cards, and Residence Permits.

> **Please Note:**  
> ID Proof is a distinct solution from Signhost's Digital Signing product. You may use this package to implement ID Proof as a standalone service or combined with the signing workflow.

The end-user receives a link to a web form to complete the identification process using one of the following methods:
- **Camera + Selfie Check:** Uses OCR (Optical Character Recognition) and facial biometrics.
- **NFC via ReadID App:** Reads the chip via the external ReadID application.
- **NFC via Signhost App:** Reads the chip directly via the Signhost application.

---

## Requirements

- PHP 8.2 or higher  
- Laravel 11 or higher  
- A [Signhost account](https://www.signhost.com) (not required for simulation mode)

---

## Getting Started with Laravel Signhost

Laravel Signhost supports both **simulation** and **production** environments.  
This lets you start developing and testing immediately without an active Signhost account.

### Simulation Mode (No Account Required)

You can start testing right away using **simulation mode**.  
This mode does not require any Signhost credentials and lets you experiment with the entire signing and identification flow using fake data.

```dotenv
SIGNHOST_MODE=simulation
```

In this mode you can:
- Create and test transactions  
- Add signers and simulate signing events  
- Explore IdProof flows safely  
- Run tests in CI environments  

All interactions happen locally, no external API requests are made.  
This makes it ideal for initial development, local demos, or automated test pipelines.

---

### Moving to a Real Signhost Account

When you’re ready to go live, switch to **production mode** and connect to your real Signhost account. All configuration values can be set via .env. The package automatically detects these values from your Laravel configuration.

```dotenv
SIGNHOST_MODE=production
SIGNHOST_USER_TOKEN=your_user_token
SIGNHOST_CLIENT_SECRET=your_client_secret
SIGNHOST_APP_KEY=your_app_key
SIGNHOST_WEBHOOK_TOKEN=your_webhook_token
SIGNHOST_WEBHOOK_SECRET=your_webhook_secret
```

**Steps to activate:**
1. Create an account at [signhost.com](https://www.signhost.com) → **Sign up**  
2. Log in to the **Developer Portal** to generate your credentials  
3. Fill in your `.env` file as shown above  
4. Start testing live transactions. Your account includes **initial test credits** so you can safely experiment before going fully live.

**Enabling Webhooks:**

To receive transaction updates from Signhost, set up a webhook in your Signhost account using your **webhook secret** and **token**.  The default webhook URL this package provides is:
   ```
   [your-url]/signhost/postback/transaction
   ```
To be able to receive webhooks, this endpoint must:
- Be publicly accessible from the internet (or use NGROK or a similar tool)
- Respond with an **HTTP 200** status to a **POST** request 

---

### Enabling Digital Identification (IdProof)

> **Please Note:**  
> To access the production API for ID proof, you **must** implement an IdProof verification endpoint that returns a 200 status code to a POST request. This step is required and cannot be skipped.

If you want to use **Digital Identification (IdProof)**, it’s recommended to first test your full implementation in **simulation mode**.

Once tested, follow these steps to enable IdProof in production:

1. **Contact Signhost Support** to request activation of the IdProof service  
   → [https://www.signhost.com/nl/bedrijf/contact](https://www.signhost.com/nl/bedrijf/contact)  
2. A Signhost representative will help you set up the service. Be aware that this process can take up to 2 weeks.
3. Provide a **valid return endpoint** that can be reached publicly:  
   ```
   [your-url]/signhost/postback/idproof
   ```
   This endpoint must:
   - Be publicly accessible from the internet  
   - Respond with an **HTTP 200** status to a **POST** request  

After verification, Signhost will activate the IdProof service for your account, and you’ll be able to process real identity verifications in your Laravel application.

---

## Installation

Install the package via Composer:

```bash
composer require noardcode/laravel-signhost
```

Then publish the configuration file:

```bash
php artisan vendor:publish --tag="signhost-config"
```

This will create a file at:

```
config/signhost.php
```

You can now adjust the configuration or link environment variables.

---

## Database Migrations

Laravel Signhost includes a set of database migrations that prepare your application to store transaction data and encrypted value objects.

Run the migrations after installing the package:

```bash
php artisan migrate
```

These migrations will create the necessary tables and columns for:
- Storing **Signhost transactions** and their metadata  
- Tracking **uploaded and signed files**  

If you want to inspect or customize these migrations, you can publish them using:

```bash
php artisan vendor:publish --tag="signhost-migrations"
```

This will copy all migration files from the package into your `database/migrations` directory, allowing you to modify or extend them as needed.

---

## Example Usage: Signing Workflows

### Creating a basic transaction for a signature

This example shows how to create a new transaction and upload a PDF file.

```php
use Illuminate\Support\Facades\Storage;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Facades\Signhost;
use Noardcode\LaravelSignhost\ValueObjects\Transaction as TransactionVO;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileUpload;
use Noardcode\LaravelSignhost\Collections\TransactionSignersCollection;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Signer;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Verifications\Scribble;

// Define your signer(s)
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

// Create a new transaction object
$transaction = new TransactionVO(
    language: Language::Dutch,
    signers: $signers,
    receivers: null,
    reference: 'agreement-2025-001',
    sendEmailNotifications: false,
);

// Select and upload your unsigned PDF
$localPath = Storage::disk('local')->path('unsigned.pdf');
$file = new FileUpload($localPath, 'unsigned.pdf');

// 1) Create the transaction in Signhost
$storedTransaction = Signhost::signing()->create($transaction, $file);

// 2) Upload file(s) to Signhost
$storedTransaction = Signhost::signing()->createFiles($storedTransaction);

// 3) Start the transaction – this triggers sending of the signing link
Signhost::signing()->startTransaction($storedTransaction);
```

---

### Using FormSets to create a more advanced transaction

This example shows how you can use FormSets to create a more advanced transaction. This will allow you to create a transaction where you can specify where the user should interact with the documents while signing.

```php
use Illuminate\Support\Facades\Storage;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Facades\Signhost;
use Noardcode\LaravelSignhost\ValueObjects\Transaction as TransactionVO;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileUpload;
use Noardcode\LaravelSignhost\Collections\TransactionSignersCollection;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Signer;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Verifications\Scribble;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\FileMetaData;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\FileMetaData\FormSet;

// Define your signer(s)
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
        // Wish to verify the signer's phone number?
        authentications: [
            new \Noardcode\LaravelSignhost\ValueObjects\Transactions\Authentications\Phonenumber('+31600000000')
        ],
        language: Language::Dutch,
        scribbleName: 'John Doe'
    ),
]);

// Create a new transaction object
$transaction = new TransactionVO(
    language: Language::Dutch,
    signers: $signers,
    receivers: null,
    reference: 'agreement-2025-001',
    sendEmailNotifications: false,
);

// Select and upload your unsigned PDF
$localPath = Storage::disk('local')->path('unsigned.pdf');
$file = new FileUpload($localPath, 'unsigned.pdf');

// Specify the formSet you want to use
$formSet = new FormSet(
    name: 'MyCustomFormSet',
    fieldTypes: [
        new FormSet\FieldType(
            name: 'FirstNameInput',
            formSetType: FormSetType::Signature,
            location: new FormSet\Location(
                search: 'FirstName: ',
                occurence: 1,
            )
        ), 
        new FormSet\FieldType(
            name: 'Signature',
            formSetType: FormSetType::Check,
            location: new FormSet\Location(
                search: 'Signature here: ',
                occurence: 1,
            )
        )
    ]
);

// Set the formSet to use on the fileUploadVO
$fileUploadVO->setFormSet($formset);

// Specify which signer(s) should use which formSet
$signerFormSets = array_map(
    fn($signer) => new FileMetaData\Signer(
        id: $signer->getId(),
        formSets: ['DummyFormSet']
    ),
    $signers
);

// Set the fileMetaData to use the specified formSet
$fileUploadVO->setFileMetaData(
    new FileMetaData(
        displayOrder: 0,
        displayName: 'My Contract',
        setParaph: false,
        signers: new TransactionFileMetaDataSignersCollection($signerFormSets)
    )
);

// After this we can create the transaction as normal

```

---

### Storing document and receipt from a finished transaction

You can store the signed document(s) and the signing receipt for verification. This is not done automatically, but you can do it manually or use the provided helper methods. 

See the [Events](#events) section below to learn how you can use package events to trigger actions when transaction data becomes available.

```php
use Illuminate\Support\Facades\Storage;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Facades\Signhost;
use Noardcode\LaravelSignhost\ValueObjects\Transaction as TransactionVO;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileUpload;
use Noardcode\LaravelSignhost\Collections\TransactionSignersCollection;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Signer;

// 1) After the signer completes, download the signed document(s)
Signhost::signing()->getSignedFiles($storedTransaction);

// 2) Download the signing receipt for verification
Signhost::signing()->getReceipt($storedTransaction);

```

---

## Example Usage: IdProof Workflows

### Digital Identification (IdProof)

Given that you have enabled IdProof in your Signhost account and created a valid form to validate the identity of the signer, you can now start the IdProof process.

```php
use Noardcode\LaravelSignhost\Facades\Signhost;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\ValueObjects\IdProof as IdProofVO;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Signer;

// 1) Create your own identifier for the signer
$identifier = 'your-own-identifier';

// 2) Redirect the user to the Signhost verification page
return Signhost::idproof()->redirectToSignhost($identifier);

```

> **Please Note:**  
> Create your own identifier for the signer/entity and store it in your application. We do not store this identifier in our database, this is up to you.

---

### Handling the Postback

Depending on the actions the user takes in the Signhost verification page, Signhost will send a postback to your application.

This package uses a default URL to handle postbacks and store them in the database, it is unnecessary to handle the postbacks yourself. The default URL is: 
   ```
   [your-url]/signhost/postback/transaction
   ```
Here we store the incoming data in the database and fire the `SignhostIdProofCreated` event once we stored the data. You can subscribe to this event to trigger your own actions, like retrieving the receipt.

### Retrieving the Dossier documents

This package does not automatically retrieve the dossier and receipt from Signhost. By subscribing to the `SignhostIdProofCreated` event, you know when the dossier can be retrieved.

```php
use Noardcode\LaravelSignhost\Facades\Signhost;
use Noardcode\LaravelSignhost\Events\SignhostIdProofCreated;
use Noardcode\LaravelSignhost\Models\Transaction;
use Illuminate\Support\Facades\Event;

// Subscribe to the event and retrieve the dossier
Event::listen(SignhostIdProofCreated::class, function (Transaction $transaction) {
    // Do your custom logic here to retrieve the dossier
    YourCustomJob::dispatch($transaction);
});
```

Next, you can use the getDossier method to retrieve the dossier.

```php
use Noardcode\LaravelSignhost\Facades\Signhost;

// Retrieve dossier
Signhost::idproof()->getDossier($transactionId, $fileId);

```

To retrieve the receipt, use the getReceipt method.

```php
use Noardcode\LaravelSignhost\Facades\Signhost;

// Retrieve dossier
Signhost::idproof()->getReceipt($transactionId);

```

---
## Using simulation mode
This package comes with a build in simulation mode that can be used to test your application without making actual API calls. This works using the `SIGNHOST_MODE=simulation` environment variable, which can be set in your `.env` file.

This makes it so all interactions happen locally, no external API requests are made. This makes it ideal for initial development, local demos, or automated test pipelines.

> **Please Note:**  
> Because the API logic depends on the data you provide, simulation mode can only generate a limited set of fake data. As a result, not all edge cases are covered, and you may need to write your own tests for comprehensive coverage.

### Simulating a Signing Activity Webhook

This Artisan command allows you to simulate a **Signhost signing activity webhook** locally or in tests. It generates a complete fake payload (based on a real Signhost structure) and posts it to your app’s configured webhook endpoint. It is important to note that this command only works when the application is in **simulation mode**.

The following command will generate a fake payload for the order with the given identifier and post it to the configured webhook endpoint:

```bash
php signhost:fake-webhook 8bbcb7df-dc34-4bac-9637-631b447cf610
```

After calling this command with the correct uuid of the transaction, the command will prompt you to choose the activity you want to simulate. These are based on the official documentation of Signhost.

### Simulating a Signhost ID Proof Webhook

This Artisan command allows you to simulate a **Signhost ID Proof webhook** locally or in tests. It generates a complete fake payload (based on a real Signhost structure) and posts it to your app’s configured webhook endpoint. It is important to note that this command only works when the application is in **simulation mode**.
> **Please Note:**  
> It is also important to note that it is not possible to fake a failed check, due to the way the Signhost API works.

The following command will generate a fake payload for the order with the given identifier and post it to the configured webhook endpoint:

```bash
php artisan signhost:fake-id-proof-webhook my-custom-identifier
```

You can also provide a custom transaction ID and postback URL:

```bash
php artisan signhost:fake-id-proof-webhook order-12345 --transaction-id=123e4567-e89b-12d3-a456-426614174000
php artisan signhost:fake-id-proof-webhook order-12345 --postback-url="https://example.test/webhooks/signhost"
```

---

## Events

To help you integrate Signhost into your application, this package fires a number of events. This means you can subscribe to events and react to them in your application and use them to trigger additional actions. For example, when a transaction is completed, you can start a job to download the files or send a notification to your users .

For example, use SignhostTransactionFinalized to trigger post-signing document storage.

| Event | Trigger |
|-------|----------|
| **SignhostIdProofCreated** | After creating an IdProof transaction |
| **SignhostIdProofReceived** | When IdProof postback is received |
| **SignhostTransactionActivity** | On any transaction activity update |
| **SignhostTransactionCreated** | After creating a transaction |
| **SignhostTransactionDeleted** | After deleting a transaction |
| **SignhostTransactionFinalized** | When Signhost webhook reports completion |
| **SignhostTransactionStarted** | After starting a transaction |

---

## Storage Structure

Files are stored in 'storage/app/' using the following structure:

```
transactions/{transaction_id}/original/{md5(transaction_uuid+file_id)}.pdf
transactions/{transaction_id}/signed/{md5(transaction_uuid+file_id)}.pdf
transactions/{transaction_id}/receipt/{md5(transaction_uuid)}.pdf
transactions/{transaction_id}/idproof/{file_id}.pdf
```

---

## Contributing

Thank you for considering contributing to this package! You can read the contribution guide [here](CONTRIBUTING.md).

---

## Security Vulnerabilities

If you discover a security vulnerability within this package, please contact us privately via [https://www.noardcode.nl/contact](https://www.noardcode.nl/contact).

Please do not disclose security vulnerabilities publicly or via GitHub Issues.

All security vulnerabilities will be promptly reviewed and addressed, and you will receive a response as soon as possible.

---

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

---

## Support

For Signhost services:   
[https://www.signhost.com/nl/bedrijf/contact](https://www.signhost.com/nl/bedrijf/contact)

For technical support on this package:  
[https://www.noardcode.nl/contact](https://www.noardcode.nl/contact)

Official API documentation:  
[https://evidos.github.io](https://evidos.github.io)

For bugs or feature requests, please open an issue at:  
https://github.com/noardcode/laravel-signhost/issues
