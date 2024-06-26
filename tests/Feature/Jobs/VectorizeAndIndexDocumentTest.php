<?php

namespace Tests\Feature\Jobs;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Jobs\ProcessPDFDocument;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Resources\Embeddings;
use OpenAI\Responses\Embeddings\CreateResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VectorizeAndIndexDocumentTest extends TestCase
{
    use RefreshDatabase;
    
    #[Test]
    public function it_marks_the_document_as_process()
    {
        $this->mockPineconeClient();
        OpenAI::fake([
            CreateResponse::fake(['Sample pdf data']),
        ]);

        $this->setFakeFixtureDisk();

        $document = Document::factory()->create([
            'type' => DocumentType::PDF,
            'path' => 'sample.pdf',
            'status' => DocumentStatus::PENDING,
            'processed_at' => null,
        ]);

        $this->assertNull($document->content);

        ProcessPDFDocument::dispatchSync($document);

        $this->assertTrue($document->refresh()->status == DocumentStatus::PROCESSED);
        $this->assertNotNull($document->processed_at);
    }

    #[Test]
    public function it_will_store_the_error_if_exception_happened()
    {
        $this->mockPineconeClient();
        OpenAI::fake([
            CreateResponse::fake(),
        ]);

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
    public function it_will_call_the_openai_embeddings_api()
    {
        $this->mockPineconeClient();
        OpenAI::fake([
            CreateResponse::fake(['Sample pdf data']),
        ]);

        $this->setFakeFixtureDisk();

        $document = Document::factory()->create([
            'type' => DocumentType::PDF,
            'path' => 'sample.pdf',
        ]);

        ProcessPDFDocument::dispatchSync($document);

        OpenAI::assertSent(Embeddings::class);
    }

    #[Test]
    public function it_will_call_the_pinecone_upsert_api()
    {
        $this->mockPineconeClient('vectors/upsert');
        
        OpenAI::fake([
            CreateResponse::fake(['Sample pdf data']),
        ]);

        $this->setFakeFixtureDisk();

        $document = Document::factory()->create([
            'type' => DocumentType::PDF,
            'path' => 'sample.pdf',
        ]);

        try {
            ProcessPDFDocument::dispatchSync($document);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail('ProcessPDFDocument::dispatchSync threw an exception: '.$e->getMessage());
        }
    }
}
