<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use League\CommonMark\Node\Block\Document;

class DocumentManagerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_queue_proper_job_when_new_document_is_added()
    {
        Queue::fake();

        $document = Document::factory()->create([
            'type' => DocumentType::PDF,
        ]);

        app(DocumentManager::class)->process($document);

        Queue::assertPushed(ProcessPDFDocument::class, function ($job) use ($document) {
            return $job->document->is($document);
        });

        Queue::assertNotPushed(ProcessWebPageDocument::class);
        
    }
}
