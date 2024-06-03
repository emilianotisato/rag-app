<?php

namespace Tests\Feature\Jobs;

use Tests\TestCase;
use App\Models\Document;
use App\Enums\DocumentType;
use App\Jobs\ProcessPDFDocument;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProcessPDFDocumentTest extends TestCase
{
    use RefreshDatabase;
    
    #[Test]
    public function it_store_the_raw_text()
    {
        $this->app->config->set('filesystems.default', 'test_files');
        $this->app->config->set('filesystems.disks.test_files', [
                'driver' => 'local',
                'root' => base_path('tests/Fixtures'),
                'throw' => false,
        ]);


        $document = Document::factory()->create([
            'type' => DocumentType::PDF,
            'path' => 'sample.pdf',
        ]);

        $this->assertNull($document->content);

        ProcessPDFDocument::dispatchSync($document);

        $this->assertNotNull($document->refresh()->content);
    }
}
