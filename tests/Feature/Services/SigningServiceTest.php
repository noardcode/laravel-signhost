<?php

namespace Noardcode\LaravelSignhost\Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionSignersCollection;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Events\SignhostTransactionCreated;
use Noardcode\LaravelSignhost\Events\SignhostTransactionStarted;
use Noardcode\LaravelSignhost\Facades\Signhost;
use Noardcode\LaravelSignhost\Facades\SignhostClient;
use Noardcode\LaravelSignhost\Models\Transaction;
use Noardcode\LaravelSignhost\Tests\TestCase;
use Noardcode\LaravelSignhost\ValueObjects\Transaction as TransactionVO;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileUpload;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Signer;

class SigningServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Configure API keys to satisfy header composition (values don't matter for fakes).
        config()->set('signhost.api.user_token', 'test-user-token');
        config()->set('signhost.api.app_key', 'test-app-key');

        // Use the fixtures disk for storing files in tests
        config()->set('signhost.disk', self::TESTING_FIXTURES_DISK);
    }

    public function test_create_create_files_start_and_get_receipt_flow_works(): void
    {
        Event::fake([SignhostTransactionCreated::class, SignhostTransactionStarted::class]);

        // Arrange faked HTTP responses
        SignhostClient::fake();

        // Build a basic transaction with one signer
        $signers = new TransactionSignersCollection([
            new Signer(email: 'john@example.com', language: Language::Dutch),
        ]);

        $transactionVO = new TransactionVO(
            language: Language::Dutch,
            seal: false,
            signers: $signers,
            receivers: null,
            reference: 'agreement-001',
            sendEmailNotifications: false,
        );

        // Use the dummy PDF from tests/Assets via the fixtures disk
        $localPath = Storage::disk(self::TESTING_FIXTURES_DISK)->path('dummy.pdf');
        $fileUpload = new FileUpload($localPath, 'unsigned.pdf');

        // Act: create the transaction (persisted locally via repository)
        $stored = Signhost::signing()->create($transactionVO, $fileUpload);

        // Assert created
        $this->assertInstanceOf(Transaction::class, $stored);
        Event::assertDispatched(SignhostTransactionCreated::class);
        $this->assertDatabaseHas('sh_transactions', ['id' => $stored->id]);
        $this->assertCount(1, $stored->files()->get());

        // Act: upload files to Signhost using the client faker
        $stored = Signhost::signing()->createFiles($stored);
        $stored->refresh();

        $file = $stored->files()->first();
        $this->assertTrue((bool) $file->content_exported, 'File content should be marked as exported after upload');

        // Act: start transaction (no-op check, but event should dispatch)
        $started = Signhost::signing()->startTransaction($stored);
        $this->assertSame($stored->id, $started->id);
        Event::assertDispatched(SignhostTransactionStarted::class);

        // Act: get receipt (stored to disk and path persisted)
        Signhost::signing()->getReceipt($stored);
        $stored->refresh();
        $this->assertNotNull($stored->receipt);
        $this->assertTrue(Storage::disk(self::TESTING_FIXTURES_DISK)->exists($stored->receipt));
    }
}
