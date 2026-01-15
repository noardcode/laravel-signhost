<?php

namespace Noardcode\LaravelSignhost\Tests\Unit\Facades;

use Noardcode\LaravelSignhost\Facades\SignhostClient;
use Noardcode\LaravelSignhost\Tests\TestCase;

class SignhostClientTest extends TestCase
{
    public function test_default_headers_include_vendor_accept_and_keys(): void
    {
        config()->set('signhost.api.user_token', 'user-token');
        config()->set('signhost.api.app_key', 'app-key');

        $client = SignhostClient::getClient();
        $headers = $client->getHeaders();

        $this->assertSame('application/vnd.signhost.v1+json', $headers['Accept']);
        $this->assertSame('APIKey user-token', $headers['Authorization']);
        $this->assertSame('APPKey app-key', $headers['Application']);
    }

    public function test_headers_can_override_accept(): void
    {
        config()->set('signhost.api.user_token', 'user-token');
        config()->set('signhost.api.app_key', 'app-key');

        $client = SignhostClient::getClient();
        $headers = $client->getHeaders(['Accept' => 'application/pdf']);

        $this->assertSame('application/pdf', $headers['Accept']);
    }
}
