<?php

namespace Tests\Feature\Jobs;

use App\Enums\DocumentType;
use App\Jobs\ProcessPDFDocument;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Resources\Embeddings;
use OpenAI\Responses\Embeddings\CreateResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProcessPDFDocumentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_store_the_raw_text()
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

        $this->assertNull($document->content);

        ProcessPDFDocument::dispatchSync($document);

        $this->assertNotNull($document->refresh()->content);
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
