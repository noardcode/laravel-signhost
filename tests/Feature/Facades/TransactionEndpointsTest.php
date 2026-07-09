<?php

namespace Noardcode\LaravelSignhost\Tests\Feature\Facades;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Noardcode\LaravelSignhost\Enums\TransactionStatus;
use Noardcode\LaravelSignhost\Exceptions\SignhostException;
use Noardcode\LaravelSignhost\Facades\Client\Endpoints\Transaction as TransactionEndpoint;
use Noardcode\LaravelSignhost\Facades\SignhostClient;
use Noardcode\LaravelSignhost\Models\Transaction;
use Noardcode\LaravelSignhost\Tests\TestCase;

class TransactionEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Configure API keys to satisfy header composition (values don't matter for fakes).
        config()->set('signhost.api.user_token', 'test-user-token');
        config()->set('signhost.api.app_key', 'test-app-key');
    }

    public function test_get_transaction_uses_faker_and_returns_payload(): void
    {
        SignhostClient::fake();

        $client = SignhostClient::getClient();
        $endpoint = new TransactionEndpoint($client);

        $data = $endpoint->getTransaction('any-id');

        $this->assertIsArray($data);
        $this->assertArrayHasKey('Id', $data);
        $this->assertNotEmpty($data['Id']);
        $this->assertArrayHasKey('Status', $data);
    }

    public function test_start_transaction_returns_successful_response(): void
    {
        SignhostClient::fake();

        $client = SignhostClient::getClient();
        $endpoint = new TransactionEndpoint($client);

        $response = $endpoint->startTransaction('dummy-id');

        $this->assertTrue($response->successful());
        $this->assertSame(200, $response->status());
    }

    public function test_start_transaction_throws_when_signhost_rejects_the_request(): void
    {
        // Registered without SignhostClient::fake()'s broader `*/transaction/*`
        // stub in play, since stub resolution matches in registration order —
        // that broader stub would otherwise shadow this more specific one.
        Http::fake([
            '*/transaction/*/start' => Http::response(
                ['Message' => 'Transaction already started'],
                400
            ),
        ]);

        $client = SignhostClient::getClient();
        $endpoint = new TransactionEndpoint($client);

        $this->expectException(SignhostException::class);

        $endpoint->startTransaction('dummy-id');
    }

    public function test_delete_transaction_returns_cancelled_status(): void
    {
        SignhostClient::fake();

        $client = SignhostClient::getClient();
        $endpoint = new TransactionEndpoint($client);

        // Build a minimal Transaction model-like object with an id using the package model
        $tx = new Transaction(['id' => 'abc-123']);

        $data = $endpoint->deleteTransaction($tx, 'No longer needed');

        $this->assertIsArray($data);
        $this->assertArrayHasKey('Status', $data);
        $this->assertSame(TransactionStatus::Cancelled->value, $data['Status']);
    }
}
