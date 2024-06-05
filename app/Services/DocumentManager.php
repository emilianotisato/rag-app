<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Jobs\ProcessPDFDocument;
use App\Jobs\ProcessWebPageDocument;
use App\Models\Document;
use Illuminate\Support\Collection;

class DocumentManager
{
    public function process(Document $document): void
    {
        if ($document->type === DocumentType::PDF) {
            ProcessPDFDocument::dispatch($document);
        } elseif ($document->type === DocumentType::WEB_PAGE) {
            ProcessWebPageDocument::dispatch($document);
        }
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
