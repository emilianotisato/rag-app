<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Probots\Pinecone\Client as PineconeClient;
use ReflectionClass;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

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

    public function mockPineconeClient(string $route = '*', array|string $returnContent = [], int $status = 200): void
    {
        $host = rtrim( config('services.pinecone.index_host'), '/') . '/';
        $route = ltrim($route, '/');
        
        MockClient::global([
            $host.$route => MockResponse::make(
                body: $returnContent,
                status: $status,
            ),
        ]);
    }
}
