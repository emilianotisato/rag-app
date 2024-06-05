<?php

namespace App\Jobs;

use App\Enums\DocumentStatus;
use App\Models\Document;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Services\DocumentManager;
use Illuminate\Support\Collection;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Probots\Pinecone\Client as Pinecone;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPDFDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Document $document)
    {
        
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fileText = Pdf::getText(
            Storage::path($this->document->path),
            config('services.pdftotext.path')
        );

        $this->document->update(['content' => $fileText]);

        $content = app(DocumentManager::class)->chunk($this->document);

        $embeddings = OpenAI::embeddings()->create([
            'model' => 'text-embedding-3-small',
            'input' => $content->toArray(),
            ])->embeddings;
        $pinecone = new Pinecone(config('services.pinecone.api_key'), config('services.pinecone.index_host'));

        collect($embeddings)->chunk(20)->each(function (Collection $chunk, $chunkIndex) use ($pinecone, $content) {
            $pinecone->data()->vectors()->upsert(
                vectors: $chunk->pluck('embedding')->map(fn ($embedding, $index) => [
                    'id' => (string) ($chunkIndex * 20 + $index),
                    'values' => $embedding,
                    'metadata' => [
                        'text' => $content[$chunkIndex * 20 + $index],
                        'document_id' => $this->document->id,
                        'type' => $this->document->type->value,
                    ]
                ])->toArray(),
            );
        });

        $this->document->update([
            'status' => DocumentStatus::PROCESSED,
            'processed_at' => now(),
        ]);
            
    }
}
