<?php

namespace App\Jobs;

use App\Models\Document;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;

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
    }
}
