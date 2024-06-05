<?php

namespace Tests\Feature\Jobs;

use Tests\TestCase;
use App\Models\Document;
use App\Enums\DocumentType;
use App\Enums\DocumentStatus;
use App\Jobs\ProcessPDFDocument;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProcessPDFDocumentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_store_the_raw_text()
    {
        $this->setFakeFixtureDisk();

        $document = Document::factory()->create([
            'type' => DocumentType::PDF,
            'path' => 'sample.pdf',
        ]);

        $this->assertNull($document->content);

        ProcessPDFDocument::dispatchSync($document);

        $this->assertNotNull($document->refresh()->content);
    }

    #[Test]
    public function it_will_store_the_error_if_exception_happened()
    {
        $this->setFakeFixtureDisk();

        $document = Document::factory()->create([
            'type' => DocumentType::PDF,
            'path' => 'unreachable.pdf',
            'status' => DocumentStatus::PENDING,
            'errors' => null,
        ]);

        $this->assertNull($document->content);

        ProcessPDFDocument::dispatchSync($document);

        $this->assertTrue($document->refresh()->status == DocumentStatus::FAILED);
        $this->assertNotNull($document->refresh()->errors);
    }

    #[Test]
    public function it_dispatches_the_vectorize_job()
    {
        Queue::fake();
        $document = Document::factory()->create();

        ProcessPDFDocument::dispatchSync($document);

        Queue::assertPushed(ProcessPDFDocument::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    }
}
