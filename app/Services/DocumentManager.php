<?php

namespace App\Services;

use App\Models\Document;
use PhpParser\Comment\Doc;
use App\Enums\DocumentType;
use App\Jobs\ProcessPDFDocument;
use App\Jobs\ProcessTextDocument;
use Illuminate\Support\Collection;
use App\Jobs\ProcessWebPageDocument;

class DocumentManager
{
    public function process(Document $document): void
    {
        match ($document->type) {
            DocumentType::PDF => ProcessPDFDocument::dispatch($document),
            DocumentType::WEB_PAGE => ProcessWebPageDocument::dispatch($document),
            DocumentType::RAW_TEXT =>  ProcessTextDocument::dispatch($document),
            default => throw new \Exception('Unsupported document type'),
        };
    }

    public function chunk(Document $document): Collection
    {
        $chunks = collect(explode("\n", $document->content))->reduce(function ($carry, $item) {
			$last = count($carry) - 1;
			if (isset($carry[$last]) && strlen($carry[$last].$item) <= config('services.chunker.size')) {
				$carry[$last] .= $item;
			} else {
				$carry[] = $item;
			}
		
			return $carry;
		}, []); // initial value is an empty array
		
		return collect($chunks);
    }
}
