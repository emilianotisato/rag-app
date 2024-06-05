<?php

namespace App\Jobs;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTextDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Document $document)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // replace paragraph tags with new line characters
        $this->document->content = str_replace('</p>', "\n", $this->document->content);
        $this->document->content == preg_replace('#<br\s*/?>#i', "\n", $this->document->content);


        $this->document->content = strip_tags($this->document->content);
        $this->document->save();

        VectorizeAndIndexDocument::dispatch($this->document);
    }
}
