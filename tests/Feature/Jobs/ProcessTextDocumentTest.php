<?php

namespace Tests\Feature\Jobs;

use Tests\TestCase;
use App\Models\Document;
use App\Jobs\ProcessTextDocument;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Process;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

class ProcessTextDocumentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_sanitize_the_text_provided()
    {
        $document = Document::factory()->create([
            'content' => '<p>Hello, World!</p><p>How are you?</p>',
        ]);

        ProcessTextDocument::dispatchSync($document);

        $this->assertEquals("Hello, World!\nHow are you?\n", $document->refresh()->content);
    }

    #[Test]
    public function it_dispatches_the_vectorize_job()
    {
        Queue::fake();
        $document = Document::factory()->create();

        ProcessTextDocument::dispatchSync($document);

        Queue::assertPushed(ProcessTextDocument::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    }
}
