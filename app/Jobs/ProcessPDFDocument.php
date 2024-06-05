<?php

namespace App\Jobs;

use App\Enums\DocumentStatus;
use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;

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
        try {
            $fileText = Pdf::getText(
                Storage::path($this->document->path),
                config('services.pdftotext.path')
            );

            $this->document->update(['content' => $fileText]);

            VectorizeAndIndexDocument::dispatch($this->document);
        } catch (\Throwable $th) {
            $this->document->update([
                'status' => DocumentStatus::FAILED,
                'errors' => [
                    'message' => $th->getMessage(),
                    'trace' => $th->getTrace(),
                ],
            ]);
        }
    }
}
