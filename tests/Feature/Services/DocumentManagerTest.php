<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Models\Document;
use App\Enums\DocumentType;
use App\Jobs\ProcessPDFDocument;
use App\Services\DocumentManager;
use App\Jobs\ProcessWebPageDocument;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    #[Test]
    public function it_can_create_an_collection_of_known_sized_chunks_from_document_raw_content()
    {
        $this->app->config->set('services.chunker', [
            'size' => 500,
        ]);
        $document = Document::factory()->create([
            'content' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. \nUt enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\nLorem ipsum dolor sit amet consectetur adipisicing elit. Odit assumenda nam illo accusamus laudantium pariatur ut doloremque ad, ex id aperiam. Optio quis saepe voluptatum consectetur tenetur nobis modi sit?",
        ]);

        $chunks = app(DocumentManager::class)->chunk($document);

        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', $chunks->first());

        $this->assertEquals('Lorem ipsum dolor sit amet consectetur adipisicing elit. Odit assumenda nam illo accusamus laudantium pariatur ut doloremque ad, ex id aperiam. Optio quis saepe voluptatum consectetur tenetur nobis modi sit?', $chunks->last());
    }

    #[Test]
    public function if_the_chunk_exceed_it_will_include_it_in_full_anyway()
    {
        $this->app->config->set('services.chunker', [
            'size' => 500,
        ]);
        $document = Document::factory()->create([
            'content' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet consectetur adipisicing elit. Odit assumenda nam illo accusamus laudantium pariatur ut doloremque ad, ex id aperiam. Optio quis saepe voluptatum consectetur tenetur nobis modi sit?",
        ]);
        
        $chunks = app(DocumentManager::class)->chunk($document);
        
        $this->assertEquals(1, $chunks->count());

        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet consectetur adipisicing elit. Odit assumenda nam illo accusamus laudantium pariatur ut doloremque ad, ex id aperiam. Optio quis saepe voluptatum consectetur tenetur nobis modi sit?', $chunks->first());
    }
}
