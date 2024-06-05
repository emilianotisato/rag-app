<?php

namespace Tests;

use ReflectionClass;
use Saloon\Http\Faking\Fixture;
use Saloon\Http\PendingRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Probots\Pinecone\Client as PineconeClient;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setFakeFixtureDisk(): void
    {
        $this->app->config->set('filesystems.default', 'test_files');
        $this->app->config->set('filesystems.disks.test_files', [
            'driver' => 'local',
            'root' => base_path('tests/Fixtures'),
            'throw' => false,
        ]);
    }

    public function mockPineconeClient(string $route = '*', Fixture|array|string $returnContent = [], int $status = 200): void
    {
        $host = rtrim( config('services.pinecone.index_host'), '/') . '/';
        $route = ltrim($route, '/');

        MockClient::destroyGlobal();
        if($returnContent instanceof Fixture) {
            MockClient::global([
                $host.$route => $returnContent
            ]);
        } else {
            MockClient::global([
                $host.$route => MockResponse::make(
                    body: $returnContent,
                    status: $status,
                ),
            ]);
        }
        
    }
}
